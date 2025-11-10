<?php

namespace App\Services\Account;

use App\Http\Requests\MerchantViaSignatureRequest;
use App\Mail\Dormancy\DormancyAutoDeductMailer;
use App\Mail\Dormancy\DormancySuspensionMailer;
use App\Mail\Fee\MaintenanceAutoDeductMailer;
use App\Repositories\Account\AccountInterface;

use App\Repositories\ExchangeRate\ExchangeRateInterface;
use App\Repositories\Transaction\TransactionInterface;
use App\Repositories\User\UserInterface;
use App\Repositories\Fee\FeeInterface;
use App\Services\BaseService;
use App\Traits\Signature;

use App\Traits\Transact;
use App\User;
use Auth;
use Carbon\Carbon;
use App\Account;
use App\Transaction;
use App\Currency;
use App\Jobs\UpdateBalanceJob;
use DB;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Collection as CollectionEloquent;
use Illuminate\Support\Collection as CollectionSupport;
use Validator;
use Illuminate\Support\Facades\App;

class AccountService extends BaseService
{
    use Transact, Signature;

	const LOGS_DORMANCY = 'CHECKING ACCOUNT DORMANCY:';
    const LOGS_MONTHLY_FEE = 'CHECKING ACCOUNT MAINTENANCE:';
    const LOGS_SIGNATURE = 'MERCHANT ACCOUNT VIA SIGNATURE:';

	const PERSONAL_DAYS_DORMANT = 90; //days
    const CURRENCY_EUR = 'EUR';
    const PERSONAL_FEE = 15;
    const BUSINESS_MONTHLY_FEE_DAY = 1;

    const STATUS_COMPLETED = 'completed';
    
	const API_ERROR_UNEXPECTED = array('code' => 'E-ACCOUNT-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);
    const API_ERROR_MERCHANT_NOT_FOUND = array('code' => 'E-ACCOUNT-001' , 'message' => 'Merchant not found', 'http_code' => 401);
    const API_ERROR_MERCHANT_ONLY = array('code' => 'E-ACCOUNT-002' , 'message' => 'Invalid account type. Must be a business account.', 'http_code' => 401);

    const API_ERROR_SIGNATURE_REQUEST = array('code' => 'E-ACCOUNT-003' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_ACCOUNT_NOT_FOUND = array('code' => 'E-ACCOUNT-004' , 'message' => 'Account Number not found.', 'http_code' => 400);
    const API_ERROR_PERSONAL_ACCOUNT = array('code' => 'E-ACCOUNT-005' , 'message' => 'For business account only.', 'http_code' => 400);
    const API_ERROR_INVALID_SIGNATURE = array('code' => 'E-ACCOUNT-006' , 'message' => 'Invalid Signature', 'http_code' => 400);
    const API_ERROR_NO_MID = array('code' => 'E-ACCOUNT-007' , 'message' => 'Please contact support, no MID in account.', 'http_code' => 400);

    protected $accountInterface;
    protected $userInterface;
    protected $exchangeRateInterface;
    protected $feeInterface;

    public function __construct(
    	AccountInterface $accountInterface,
    	UserInterface $userInterface,
        ExchangeRateInterface $exchangeRateInterface,
        TransactionInterface $transactionInterface,
        FeeInterface $feeInterface
    ) {
        $this->accountInterface = $accountInterface;
        $this->userInterface = $userInterface;
        $this->exchangeRateInterface = $exchangeRateInterface;
        $this->transactionInterface = $transactionInterface;
        $this->feeInterface = $feeInterface;
    }

    public function depositMoney($payload)
    {
        return $this->accountInterface->updateAmountByUserIdAndCurrency($payload->user_id, $payload->currency, $payload->amount);
    }

    public function dormancy(int $user_id = 0) : string
    {
    	try {
            $hasUserId = $user_id > 0 ? true : false;
    		Log::info(self::LOGS_DORMANCY);
            if(App::environment('staging') && $hasUserId) {
                $user = $this->userInterface->get($user_id);
                $this->processDormancyChecker($user);
            }else{
                $users = $this->userInterface->getAll();
                foreach($users as $user) {
                    $this->processDormancyChecker($user);
                }
            }
            return $this->sendResponse([], 'Dormancy crawl executed');

    	} catch(Exception $e) {
    		$message = $this->getErrorMessage($e);
            report($e);
            Log::error(self::LOGS_DORMANCY . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
    	}
    }

    public function get() : object
    {
        $user = Auth::user();
        $accounts = $this->accountInterface->getAccountsByUserId($user->id);
        $accounts->makeHidden(['opening_balance_in_money_format', 'owner']);

        $currencies = Currency::where('status', 1)->get();
        $accountsToMerge = [];
        foreach($currencies as $currency){
            if(!$accounts->contains('currency', $currency->name)){
                $accountArr = [];
                $accountArr['id'] = 0;
                $accountArr['user_id'] = $user->id;
                $accountArr['account_type_id'] = '';
                $accountArr['currency'] = $currency->name;
                $accountArr['status'] = '';
                $accountArr['opening_balance'] = '0.00';
                $accountArr['account_number'] = $user->account_number;
                $accountsToMerge[] = $accountArr;
            }
        }
        $accounts =  $accounts->transform(function($account){
                            $payload = new \stdClass();
                            $payload->id = !is_null($account->id) ? $account->id : 0;
                            $payload->user_id = !is_null($account->user_id) ? $account->user_id : '';
                            $payload->account_type_id = !is_null($account->account_type_id) ? $account->account_type_id : '';
                            $payload->currency = !is_null($account->currency) ? $account->currency : '';
                            $payload->status = !is_null($account->status) ? $account->status : '';
                            $payload->opening_balance = !is_null($account->opening_balance) ? $account->opening_balance : '0.00';
                            $payload->account_number = !is_null($account->account_number) ? $account->account_number : '';
                            return $payload;
                        })->all();
        $accounts = array_merge($accounts, $accountsToMerge);

        return $this->sendResponse($accounts, '');
    }

    /* ajax request only */
    public function getMerchantViaSignature($request) : object
    {
        try {
            Log::info(self::LOGS_SIGNATURE);
            $validator = $this->validateMerchantViaSignature($request);

            if ($validator->fails()) {
                if($request->ajax()){
                     return $this->sendError(self::API_ERROR_SIGNATURE_REQUEST, $validator->errors());
                }
            }

            $merchant = $this->userInterface->getByAccountNumber($request->account_number);
            if (is_null($merchant)) {
                return $this->sendError(self::API_ERROR_ACCOUNT_NOT_FOUND, '');
            }

            if ($merchant->account_type == 'personal') {
                return $this->sendError(self::API_ERROR_PERSONAL_ACCOUNT, '');
            }

            if (empty($merchant->mid)) {
                return $this->sendError(self::API_ERROR_NO_MID, '');
            }

            $result = $this->checkUserSignature($merchant->mid, $request->signature);
            if (!$result['valid']) {
                return $this->sendError(self::API_ERROR_INVALID_SIGNATURE, '');
            }

            $accounts = $this->accountInterface->getAccountsByUserId($merchant->id, ['currency', 'id', 'opening_balance', 'user_id']);               
            $accounts->makeHidden(['opening_balance_in_money_format', 'owner']);

            $currencies = Currency::where('status', 1)->get();
            $accountsToMerge = [];
            foreach($currencies as $currency){
                if(!$accounts->contains('currency', $currency->name)){
                    $accountArr = [];
                    $accountArr['currency'] = $currency->name;
                    $accountArr['id'] = 0;
                    $accountArr['opening_balance'] = '0.00';
                    $accountArr['user_id'] = $merchant->id;
                    $accountArr['account_number'] = $merchant->account_number;
                    $accountsToMerge[] = $accountArr;
                }
            }
            $accounts =  $accounts->transform(function($account){
                                $payload = new \stdClass();
                                $payload->currency = !is_null($account->currency) ? $account->currency : '';
                                $payload->id = !is_null($account->id) ? $account->id : 0;
                                $payload->opening_balance = !is_null($account->opening_balance) ? $account->opening_balance : '0.00';
                                $payload->user_id = !is_null($account->user_id) ? $account->user_id : '';
                                $payload->account_number = !is_null($account->account_number) ? $account->account_number : '';
                                return $payload;
                            })->all();
            $accounts = array_merge($accounts, $accountsToMerge);

            return $this->sendResponse($accounts, '');


        }catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_SIGNATURE . '-' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    private function checkDormancy($updated_at)
    {
        $now = Carbon::now();

        $updated_at = Carbon::parse($updated_at);
        $length = $updated_at->diffInDays($now);

        return ($length > self::PERSONAL_DAYS_DORMANT);

    }

    private function processDormancyChecker(User $user) : void
    {
        if ($user->user_type == 'user' && !$user->is_dormant && $user->account_type == Account::PERSONAL && $user->is_included_on_dormancy) {
            if(in_array($user->account_status, [$this->userInterface::ACCOUNT_VERIFIED, $this->userInterface::ACCOUNT_UNVERIFIED])){
                $accounts = $this->accountInterface->getAccountsByUserIdOrderByPriority($user->id);
                if ($accounts->count()) {
                    $hasActiveAccount = false;
                    foreach ($accounts as $account) {
                        $result = $this->checkDormancy($account->updated_at);
                        if(!$result){
                            $hasActiveAccount = true;
                            break;
                        }
                    }
                    if (!$hasActiveAccount) {
                        $feeApplied = $this->checkApplyDormantFee($accounts, $user);
                        Log::info('LOG DORMANT FEE: ' . json_encode($feeApplied));
                        if (!$feeApplied) {
                            $this->makeUserDormant($user);
                        }
                    }
                } else {
                    if ($this->checkDormancy($user->created_at)) {
                        $this->makeUserDormant($user);
                    }
                }
            }
        }

    }

    public function maintenance(int $user_id = 0) : string
    {

    	try {
            
            $hasUserId = $user_id > 0 ? true : false;
            if((int)date('d') !== self::BUSINESS_MONTHLY_FEE_DAY && !App::environment('staging')){
                throw new Exception('Maintenance crawl should run every 1st of the month');
            }

            Log::info(self::LOGS_MONTHLY_FEE." - Start");
            if(App::environment('staging') && $hasUserId) {
                $user = $this->userInterface->get($user_id);
                if ($user->account_type == Account::BUSINESS && in_array($user->account_status, [$this->userInterface::ACCOUNT_VERIFIED, $this->userInterface::ACCOUNT_UNVERIFIED])) {
                    $accounts = $this->accountInterface->getAccountsByUserIdOrderByPriority($user->id);
                    if ($accounts->count()) {
                        $feeApplied = $this->checkApplyMaintenanceFee($accounts, $user);
                    }
                }
            }elseif(!$hasUserId){
                $currentMonth = date('m');
                $users = $this->userInterface->getAll();
                foreach($users as $user) {
                    if ($user->account_type == Account::BUSINESS && in_array($user->account_status, [$this->userInterface::ACCOUNT_VERIFIED, $this->userInterface::ACCOUNT_UNVERIFIED])) {
                        $accounts = $this->accountInterface->getAccountsByUserIdOrderByPriority($user->id);
                        if ($accounts->count()) {
                            // Check if there's an existing maintenance fee transaction for this month
                            $existingMaintenanceTransaction = $this->transactionInterface->getByTypeAndUserId(Transaction::TYPE_MONTHLY_FEE, $user->id, [ 'MONTH(created_at) = ? ', [$currentMonth] ]);
                            if ($existingMaintenanceTransaction->count() <= 0) {
                                $feeApplied = $this->checkApplyMaintenanceFee($accounts, $user);
                            }
                        }
                    }
                }
            }

            return $this->sendResponse([], 'Maintenance crawl executed');

    	} catch(Exception $e) {
    		$message = $this->getErrorMessage($e);
            report($e);
            Log::error(self::LOGS_MONTHLY_FEE . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
    	}

    }

    private function checkApplyMaintenanceFee(object $accounts, User $user) : bool
    {
        $isFeeApplied = false;
        $subtractedFee = $accountArr = $processedArr = [];
        $maintenanceFee = 0;
        $updateBalancePayload = collect();

        try{

            DB::beginTransaction();
            
            $fee_details = $this->feeInterface->getFeeByServiceAndAccountType(Transaction::TYPE_MONTHLY_FEE, Account::BUSINESS, $user->id);
            if(empty($fee_details->currency)){
                throw new Exception('No fee currency is set');
            }

            if( floatval($fee_details->amount) > 0 ){
                $maintenanceFee = floatval($fee_details->amount);
                $maintenanceFee = roundCurrency($maintenanceFee, $fee_details->currency, true);
            }

            if($maintenanceFee <= 0){
                throw new Exception('No monthly maintenance fee is set');
            }

            foreach($accounts as $account){
                $accountArr[$account->currency] = $account->opening_balance; 
            }
            
            if (array_key_exists($fee_details->currency, $accountArr)) {
                $balance = floatval($accountArr[$fee_details->currency]);
                if($balance > 0) {
                    $diff = $balance - $maintenanceFee;
                    if ($diff < 0) {
                        $maintenanceFee = $maintenanceFee - $balance;
                        $accountArr[$fee_details->currency] = 0;
                        $subtractedFee[$fee_details->currency] = $balance;
                    } else {
                        $accountArr[$fee_details->currency] = $diff;
                        $subtractedFee[$fee_details->currency] = $maintenanceFee;
                        $maintenanceFee = 0;
                    }
                }
            }

            if($maintenanceFee !== 0){
                foreach($accountArr as $currency => $balance){
                    $balance = floatval($balance);
                    if ($balance > 0) {
                        $maintenanceFeeConverted = $this->convertAmountByCurrency($maintenanceFee, $fee_details->currency, $currency);
                        if($maintenanceFeeConverted <= 0){
                            continue;
                        }
                        $diff = $balance - $maintenanceFeeConverted;
                        if ($diff < 0) {
                            $subtractedFee[$currency] = $balance;
                            $maintenanceFeeConverted = $maintenanceFeeConverted - $balance;
                            $maintenanceFee = $this->convertAmountByCurrency($maintenanceFeeConverted, $currency, $fee_details->currency);
                            $accountArr[$currency] = 0;
                        } else {
                            $accountArr[$currency] = $diff;
                            $subtractedFee[$currency] = $maintenanceFeeConverted;
                            $maintenanceFee = 0;
                            break;
                        }
                    }
                }
            }

            if ($maintenanceFee == 0) {
                $transaction_number = $this->createTransactionId(Transaction::TYPE_MONTHLY_FEE);
                foreach ($subtractedFee as $key => $value) {
                    if(isset($accountArr[$key])){
                        $payload = new \stdClass();
                        $payload->user_id = $user->id;
                        $payload->currency = $key;
                        $payload->amount = $accountArr[$key];
                        $account = Account::where('user_id', $user->id)->where('currency', $key)->firstOrFail();

                        $payload->fee = $value;
                        $payload->account_id = $account->id;
                        $payload->ref_id = $account->id;
                        $payload->transaction_number = $transaction_number;
                        $payload->current_balance = 0;
                        $payload->status = self::STATUS_COMPLETED;
                        $payload->note = _lang('Monthly Fee');
                        $payload->type = Transaction::TYPE_MONTHLY_FEE;
                        $transaction = $this->createFeeTransaction($payload);
                        Log::info(json_encode($transaction));

                        $updateBalancePayload->push(collect([
                            'account_id' => $transaction->account_id, 
                            'transaction_id' => $transaction->id,
                        ]));
                    }
                }

                $isFeeApplied = true;
                DB::commit();
                $updateBalancePayload->each(function ($payload) {
                    UpdateBalanceJob::dispatch($payload['account_id'], $payload['transaction_id']);
                });

                $mail = new \stdClass();
                $mail->first_name = $user->first_name;
                $mail->last_name = $user->last_name;
                $mail->account_number = $user->account_number;
                $mail->transaction_date = Carbon::now()->format('Y-m-d H:i:s A');
                $mail->transaction_number = $transaction_number;
                $mail->result = $subtractedFee;
                session(['forcedLanguage' => $user->user_information->language]);
                Mail::to($user->email)->send(new MaintenanceAutoDeductMailer($mail));
                session()->forget('forcedLanguage');

            } else {
                throw new Exception('Not enough account balance for user id: '.$user->id);
            }
               
        }catch(Exception $e){
            DB::rollback();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_MONTHLY_FEE . ' - ' . $message);
        }
        return $isFeeApplied;
    }

    private function checkApplyDormantFee(object $accounts, User $user): bool
    {
       
        $isFeeApplied = false;
        $personalFee = self::PERSONAL_FEE;
        $subtractedFee = [];
        $accountArr = $accounts->mapwithKeys(fn ($account) => [$account->currency =>  $account->opening_balance])->all();
        $updateBalancePayload = collect();

        try {
            
            DB::beginTransaction();
            if (array_key_exists(self::CURRENCY_EUR, $accountArr)) {
                $balance = floatval($accountArr[self::CURRENCY_EUR]);
                if($balance > 0) {
                    $diff = $balance - $personalFee;
                    if ($diff < 0) {
                        $personalFee = $personalFee - $balance;
                        $accountArr[self::CURRENCY_EUR] = 0;
                        $subtractedFee[self::CURRENCY_EUR] = $balance;
                    } else {
                        $accountArr[self::CURRENCY_EUR] = $diff;
                        $subtractedFee[self::CURRENCY_EUR] = $personalFee;
                        $personalFee = 0;
                    }
                }
            }
            
            if($personalFee !== 0){
                foreach($accountArr as $currency => $balance){
                    $balance = floatval($balance);
                    if ($balance > 0) {
                        $personalFeeConverted = $this->convertAmountByCurrency($personalFee, self::CURRENCY_EUR, $currency);
                        if($personalFeeConverted <= 0){
                            continue;
                        }
                        $diff = $balance - $personalFeeConverted;
                        if ($diff < 0) {
                            $subtractedFee[$currency] = $balance;
                            $personalFeeConverted = $personalFeeConverted - $balance;
                            $personalFee = $this->convertAmountByCurrency($personalFeeConverted, $currency, self::CURRENCY_EUR);
                            $accountArr[$currency] = 0;
                        } else {
                            $accountArr[$currency] = $diff;
                            $subtractedFee[$currency] = $personalFeeConverted;
                            $personalFee = 0;
                            break;
                        }
                    }
                }
            }

            if($personalFee == 0) {
                $transaction_number = $this->createTransactionId(Transaction::TYPE_INACTIVITY_FEE);
                foreach ($subtractedFee as $key => $value) {
                    if(isset($accountArr[$key])){
                        $payload = new \stdClass();
                        $payload->user_id = $user->id;
                        $payload->currency = $key;
                        $payload->amount = $accountArr[$key];
                        $account = Account::where('user_id', $user->id)->where('currency', $key)->firstOrFail();

                        $payload->fee = $value;
                        $payload->account_id = $account->id;
                        $payload->ref_id = $account->id;
                        $payload->transaction_number = $transaction_number;
                        $payload->current_balance = 0;
                        $payload->status = self::STATUS_COMPLETED;
                        $payload->note = _lang('Dormancy Fee');
                        $payload->type = Transaction::TYPE_INACTIVITY_FEE;
                        $transaction = $this->createFeeTransaction($payload);
                        Log::info(json_encode($transaction));

                        $updateBalancePayload->push(collect([
                            'account_id' => $transaction->account_id, 
                            'transaction_id' => $transaction->id,
                        ]));
                    }
                }
                $isFeeApplied = true;
                DB::commit();

                $updateBalancePayload->each(function ($payload) {
                    UpdateBalanceJob::dispatch($payload['account_id'], $payload['transaction_id']);
                });

                $mail = new \stdClass();
                $mail->first_name = $user->first_name;
                $mail->last_name = $user->last_name;
                $mail->account_number = $user->account_number;
                $mail->transaction_date = Carbon::now()->format('Y-m-d H:i:s A');
                $mail->transaction_number = $transaction_number;
                $mail->result = $subtractedFee;
                session(['forcedLanguage' => $user->user_information->language]);
                Mail::to($user->email)->send(new DormancyAutoDeductMailer($mail));
                session()->forget('forcedLanguage');
            } else {
                throw new Exception('Not enough account balance for user id: '.$user->id);
            }

        } catch(Exception $e) {
            DB::rollback();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_DORMANCY . ' - ' . $message);
        }
        return $isFeeApplied;

    }

    private function makeUserDormant(User $user): void
    {
        try {
            DB::beginTransaction();
            $this->userInterface->update($user->id, ['is_dormant' => 1]);
            $this->userInterface
                ->updateAccountStatusDate($user, $this->userInterface::ACCOUNT_DORMANT, true);
            $this->updateUserRemarks($user);
            DB::commit();

            $mail = new \stdClass();
            $mail->first_name = $user->first_name;
            $mail->last_name = $user->last_name;
            $mail->account_number = $user->account_number;
            session(['forcedLanguage' => $user->user_information->language]);
            Mail::to($user->email)->send( new DormancySuspensionMailer($mail) );
            session()->forget('forcedLanguage');
        } catch(Exception $e) {
            DB::rollBack();
    		$message = $this->getErrorMessage($e);
            Log::error(self::LOGS_DORMANCY . ' - ' . $message);
    	}
        
    }

    private function updateAcccounts($payload)
    {
        $params['user_id'] =  $payload->user_id;
        $params['currency'] = $payload->currency;

        $balance = roundCurrency($payload->amount, $payload->currency);

        $account = $this->accountInterface->where($params, 'first');
        return $this->accountInterface->update($account->id, ['opening_balance' => $balance]);

    }

    private function convertAccountsToEUR($objAccounts)
    {
        $accountInEur = [];

        //convert all balances to EUR
        foreach($objAccounts as $account) {
            if ($account->currency != self::CURRENCY_EUR) {
                $rate = $this->exchangeRateInterface->getExchangeRate($account->currency, self::CURRENCY_EUR);
                $amount = $rate * floatval($account->opening_balance);
                $accountInEur[$account->currency] = $amount;
            } else {
                $accountInEur[self::CURRENCY_EUR] = floatval($account->opening_balance);
            }
        }

        return $accountInEur;
    }

    private function convertAccountsToOrig($arrAccounts)
    {
        $accountInEur = [];

        //convert all balances to their original currency
        foreach($arrAccounts as $key => $value) {
            if ($key != self::CURRENCY_EUR) {
                $rate = $this->exchangeRateInterface->getExchangeRate(self::CURRENCY_EUR, $key);
                $amount = $rate * floatval($value);
                $accountInEur[$key] = $amount;
            } else {
                $accountInEur[self::CURRENCY_EUR] = $value;
            }
        }

        return $accountInEur;
    }


    private function convertAmountByCurrency(float $value, string $fromCurrency, string $toCurrency) : string
    { 
        $rate = $this->exchangeRateInterface->getExchangeRate($fromCurrency, $toCurrency);
        if(floatval($rate) <= 0){
            throw new Exception("Cannot get the exchange rate of {$fromCurrency} to {$toCurrency}");
        }
        $amount = $rate * floatval($value);
        return roundCurrency($amount, $toCurrency, true);
    }

    private function createTransaction($payload)
    {
        $param['user_id'] = $payload->user_id;
        $param['currency'] = $payload->currency;
        $param['transaction_number'] = $payload->transaction_number;
        $param['amount'] = 0;
        $param['fee'] = $payload->fee;
        $param['account_id'] = $payload->account_id;
        $param['dr_cr'] = 'dr';
        $param['type'] = 'inactivity_fee';
        $param['status'] = self::STATUS_COMPLETED;
        $param['ref_id'] = $payload->ref_id;
        $param['note'] = _lang('Dormancy Fee');
        $param['current_balance'] = $payload->current_balance;
        $param['created_by'] = $payload->user_id;
        $param['updated_by'] = $payload->user_id;

        $transaction = $this->transactionInterface->create($param);

        return $transaction;
    }

    private function createFeeTransaction(object $payload) : object
    {

        $param['user_id'] = $payload->user_id;
        $param['currency'] = $payload->currency;
        $param['transaction_number'] = $payload->transaction_number;
        $param['amount'] = 0;
        $param['fee'] = $payload->fee;
        $param['account_id'] = $payload->account_id;
        $param['dr_cr'] = 'dr';
        $param['type'] = $payload->type;
        $param['status'] = $payload->status;
        $param['ref_id'] = $payload->ref_id;
        $param['note'] = $payload->note;
        $param['current_balance'] = $payload->current_balance;
        $param['created_by'] = $payload->user_id;
        $param['updated_by'] = $payload->user_id;

        $transaction = $this->transactionInterface->create($param);

        return $transaction;

    }

    private function validateMerchantViaSignature($request)
    {
        $merchantViaSignatureRequest = new MerchantViaSignatureRequest();
        $rules = $merchantViaSignatureRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function updateUserRemarks($user): User
    {
        Log::info('LOG DORMANT USER: ' . json_encode($user));
        $message = "\n" . $user->user_information->account_declared_dormant_at;
        $message .= "\nChanged from " . $user->account_status . " to Dormant";
        $user->user_information->remarks = $message . "\n" . $user->user_information->remarks;
        $user->user_information->save();
        return $user;
    }
}
