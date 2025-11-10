<?php

namespace App\Services;

use App\Exports\Excel;
use App\Repositories\Account\AccountInterface;
use App\Repositories\Currency\CurrencyInterface;
use App\Repositories\User\UserInterface;
use App\Repositories\UserRepository;
use App\Repositories\WelcomeMessage\WelcomeMessageInterface;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class UserService
 */
class UserService extends BaseService
{
    const LOG_TRANSACTION_REPORT = 'LOG TRANSACTION REPORT ADMIN REQUEST';

    /**
     * @var UserRepository
     */
    protected $repository;
    protected $accountRepository;
    protected $excelExport;

    const EXPORT_HEADERS = [
        'Register Date',
        'Account Number',
        'Account Type',
        'First Name',
        'Last Name',
        'Email',
        'Affiliate Code',
        'Account Status',
        'User Type',
        'Phone Number',
        'Date of Birth',
        'Address',
        'City',
        'State',
        'Zip',
        'Country of Residence',
        'Verified Date',
        'Closed Date',
        'Suspended Date',
        'Dormancy Date',
        'Language',
        'Newsletters',
    ];

    const EXPORT_HEADERS_BALANCE = [
        'Account Number',
        'First Name',
        'Last Name',
        'USD',
        'EUR',
        'GBP',
        'JPY',
        'PHP',
        'HKD'
    ];

    const EXPORT_FILE_NAME = 'OWL-User-Export.csv';
    const EXPORT_BALANCE_FILE_NAME = 'OWL-User-Balances-Export.csv';
    const BASE_CURRENCY = 'EUR';

    const MAX_EXPORT_CHUNK_SIZE = 10000;

    /**
     * UserService constructor.
     */
    public function __construct(
        UserRepository $userRepository,
        AccountInterface $accountInterface,
        CurrencyInterface $currencyInterface,
        Excel $excelExport,
        WelcomeMessageInterface $welcomeMessageInterface,
        UserInterface $userInterface
    )
    {
        $this->welcomeMessageInterface = $welcomeMessageInterface;
        $this->repository = $userRepository;
        $this->accountInterface = $accountInterface;
        $this->currencyInterface = $currencyInterface;
        $this->excelExport = $excelExport;
        $this->userInterface = $userInterface;
    }

    /**
     * Get user by Id.
     *
     * @param int $id
     *
     * @return User
     */
    public function getById(int $id)
    {
        return $this->repository->getById($id);
    }

    /**
     * @param array $filters
     *
     * @return User
     */
    public function getAllUsers($request = null)
    {
        $filters = (isset($request) && $request != null) ? $request->input('filters') : null;
        $filters = !is_null($filters) ? $filters : ['user_type' => 'user'];

        $users = $this->repository->getAllUsers($filters);
        return !empty($users) ? $users : [];
    }

    /**
     * @param Request $request
     *
     * This method build user data and transaction
     *
     * @return array
     */
    public function getUsersDashboardData($request)
    {
        $currency = isset($request->currency) ? $request->currency : self::BASE_CURRENCY;
        $request->session()->put('currency', $currency);

        $selected_year = ($request->has('year')) ? $request->year : date('Y');

        $userData = [];
        $userData['currency'] = $currency;
        $userData['verified_user_count'] = $this->repository->getAllUsers(
            [
                'account_status' => 'Verified',
                'user_type' => 'user',
                'access_type' => 'live',
                'is_dormant' => 0
            ]
        )->count();
        $userData['unverified_user_count'] = $this->repository->getAllUsers(
            [
                'account_status' => 'Unverified',
                'user_type' => 'user',
                'access_type' => 'live',
                'is_dormant' => 0
            ]
        )->count();

        $pluck = true;
        $userData['currencies'] = $this->currencyInterface->getAllActive($pluck)->toArray();

        $transaction_options= [
            'type' => 'deposit',
            'currency' => $currency,
            'access_type' => 'live',
            'year' => $selected_year
        ];

        $userData['total_deposit'] = $this->repository->getUserTransactionsByTypeAndCurrency($transaction_options);
        $transaction_options['type'] = 'withdrawal';
        $userData['total_withdraw'] = $this->repository->getUserTransactionsByTypeAndCurrency($transaction_options);


        $earliest_year = 2020;
        $latest_year = date('Y');

        $year_list = [];
        foreach ( range( $latest_year, $earliest_year ) as $i ) {
            array_push($year_list, $i);
        }
        $year_list = array_reverse($year_list);

        $userData['year_list'] = $year_list;
        $userData['year_selected'] = $selected_year;

        return $userData;
    }

    /**
     *
     * @param int $userId
     * @param Request $request
     *
     * This method build user data and transaction
     *
     * @return array
     */
    public function getUserDashboardDataByUserId(int $userId, $request)
    {
        $userData = [];
        $welcome_message = $this->welcomeMessageInterface->getCurrentMessage();
        $language = Auth::user()->user_information->language;

        $userData['welcome_message'] = $welcome_message;
        $userData['accounts'] = $this->repository->getUserCurrencyAccountsByUserId($userId);

        $userData['recent_transactions'] = buildTransactionData($this->repository->getUserTransactionsByUserId($userId), $request);

        return $userData;
    }

    /**
     *
     * @param string $type
     * @param Request $request
     *
     * This method build user data and transaction
     *
     * @return array
     */
    public function getUserTotalTransactionPerMonthByYearAndCurrency($request, $type)
    {
        $currency = $request->session()->get('currency');
        $year = ($request->has('year')) ? $request->year : date('Y');
        $label = $type == 'deposit' ? 'Deposit' : 'Withdraw';

        $months = [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec"
                ];

        $results = $this->repository
                ->getUserTotalTransactionPerMonthByYearAndCurrency($year, $currency, $type);

        $transactions = [];
        foreach($results as $result) {
            $transactions[($result->month -1)] = $result->amount;
        }
        //clean array add missing keys
        end($months);
        $max = key($months); //Get the final key as max!

        for($i = 0; $i < $max; $i++)
        {
            if(!isset($transactions[$i]))
            {
                $transactions[$i] = 0;
            }
        }

        ksort($transactions);
        return [
            "Months" => $months,
            $label => array_values($transactions)
        ];
    }

    public function getAllUserTransactions($request)
    {
        $users = $this->getAllUsers()->sortBy('first_name');

        try {
            return view('backend.reports.all_transaction_report',compact('users'));
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_TRANSACTION_REPORT . ' - ' . $message);
            return back()->with('error', _lang('Unexpected Error Occurred: ' .  $message));
        }

    }

    public function buildTransactionData($transactions, $request): array
    {
        $transactionArray = [];

        foreach ($transactions as $transaction) {
            $transaction_data = new \stdClass();
            $user = User::where('id',$transaction->created_by)->first();

            if ($transaction->type == 'internal_transfer' || $transaction->type == 'currency_exchange') {
                $transaction_data->debit_account = Transaction::where('transactions.dr_cr', 'dr')
                    ->where('user_id', $transaction->user_id)
                    ->where('transactions.id', $transaction->id)
                    ->where('type',$transaction->type)
                    ->where('transactions.status','completed')
                    ->join('users as u', 'u.id', '=', 'transactions.user_id')
                    ->select(
                        'u.*',
                        'transactions.id as transaction_id',
                        'transactions.currency',
                        'transactions.amount',
                        'transactions.fee',
                        'transactions.account_id',
                        'transactions.dr_cr',
                        'transactions.type',
                        'transactions.note',
                        'transactions.ref_id',
                        'transactions.current_balance',
                        'transactions.approval_date'
                    )->first();

                if (isset($transaction_data->debit_account)) {
                    $transaction_data->credit_account = Transaction::where('transactions.dr_cr', 'cr')
                    ->where('transactions.parent_id', $transaction_data->debit_account->transaction_id)
                    ->where('type',$transaction->type)
                    ->where('transactions.status','completed')
                    ->join('users as u', 'u.id', '=', 'transactions.user_id')
                    ->select(
                        'u.*',
                        'transactions.id as transaction_id',
                        'transactions.currency',
                        'transactions.amount',
                        'transactions.fee',
                        'transactions.account_id',
                        'transactions.dr_cr',
                        'transactions.type',
                        'transactions.note',
                        'transactions.ref_id',
                        'transactions.current_balance',
                        'transactions.approval_date'
                    )->first();
                }


            } else if ($transaction->type == 'deposit' || $transaction->type == 'refund') {
                $transaction_data->credit_account = Transaction::where('transactions.dr_cr','cr')
                    ->where('type',$transaction->type)
                    ->where('transactions.id',$transaction->id)
                    ->where('transactions.status','completed')
                    ->join('users as u', 'u.id', '=', 'transactions.user_id')
                    ->select(
                        'u.*',
                        'transactions.id as transaction_id',
                        'transactions.currency',
                        'transactions.amount',
                        'transactions.fee',
                        'transactions.account_id',
                        'transactions.dr_cr',
                        'transactions.type',
                        'transactions.note',
                        'transactions.ref_id',
                        'transactions.current_balance',
                        'transactions.approval_date'
                    )->first();

                if ($transaction_data->credit_account && $transaction->type == 'deposit') {
                    $transaction_data->created_at = $transaction->updated_at;
                }
            } else if ($transaction->type == 'withdrawal' || $transaction->type == 'wire_transfer' || $transaction->type == 'bulk_withdrawal' || $transaction->type == 'card_topup' || $transaction->type == 'inactivity_fee') {
                $tempTransaction = Transaction::where('transactions.dr_cr','dr');
                $amount = ($transaction->type == 'bulk_withdrawal' || $transaction->type == 'card_topup') ? 'transactions.amount as amount' : 'w.debit_amount as amount';

                if ($transaction->type == 'bulk_withdrawal') {
                    $tempTransaction = $tempTransaction->where('transactions.status', 'completed');
                } else {
                    $tempTransaction = $tempTransaction->whereOr('transactions.status', 'completed')->whereOr('transactions.status', 'applying');
                }

                $transaction_data->debit_account = $tempTransaction->where('transactions.dr_cr','dr')
                    ->where('type',$transaction->type)
                    ->where('transactions.id',$transaction->id)
                    ->join('users as u', 'u.id', '=', 'transactions.user_id')
                    ->join('accounts as a', 'a.id', '=', 'transactions.account_id')
                    ->leftJoin('wire_transfer_details as w', 'w.id', '=', 'transactions.ref_id')
                    ->select(
                        'u.*',
                        'transactions.id as transaction_id',
                        'a.currency as currency',
                        'transactions.fee',
                        'transactions.account_id',
                        'transactions.dr_cr',
                        'transactions.type',
                        'transactions.note',
                        'transactions.ref_id',
                        'transactions.current_balance',
                        'transactions.approval_date',
                        $amount
                    )->first();
                $transaction_data->created_at = $transaction->updated_at;
            } else if ( $transaction->type == 'payment_request' && $transaction->status == 'completed') {
                $transaction_data->credit_account = Transaction::where('transactions.dr_cr', 'cr')
                    ->where('transactions.id',$transaction->id)
                    ->where('user_id', $transaction->user_id)
                    ->where('type',$transaction->type)
                    ->join('users as u', 'u.id', '=', 'transactions.user_id')
                    ->select(
                        'u.*',
                        'transactions.id as transaction_id',
                        'transactions.currency',
                        'transactions.amount',
                        'transactions.fee',
                        'transactions.account_id',
                        'transactions.dr_cr',
                        'transactions.type',
                        'transactions.note',
                        'transactions.ref_id',
                        'transactions.current_balance',
                        'transactions.approval_date'
                    )->first();

                if ($transaction_data->credit_account) {
                    $transaction_data->debit_account = Transaction::where('transactions.dr_cr', 'dr')
                    ->where('transactions.parent_id', $transaction_data->credit_account->transaction_id)
                    ->where('type',$transaction->type)
                    ->join('users as u', 'u.id', '=', 'transactions.user_id')
                    ->select(
                        'u.*',
                        'transactions.id as transaction_id',
                        'transactions.currency',
                        'transactions.amount',
                        'transactions.fee',
                        'transactions.account_id',
                        'transactions.dr_cr',
                        'transactions.type',
                        'transactions.note',
                        'transactions.ref_id',
                        'transactions.current_balance',
                        'transactions.approval_date'
                    )->first();
                }
            }

            if (isset($transaction_data->credit_account) || isset($transaction_data->debit_account)) {
                $transaction_data->id = $transaction->id;
                $transaction_data->created_at = $transaction->created_at;
                $transaction_data->user_type = !empty($user) ? $user->user_type : 'N/A';
                $transaction_data->user_id = $transaction->user_id;
                $transaction_data->approval_date = !is_null($transaction->approval_date) ? $transaction->approval_date : '';

                $transaction_data->type = $transaction->type;
                $transaction_data->transaction_number = $transaction->transaction_number;
                $transaction_data->note = $transaction->note;
                $transaction_data->status = $transaction->status;
                $transaction_data->debitUserId = isset($transaction_data->debit_account) ? $transaction_data->debit_account->id : '';
                $transaction_data->creditUserId = isset($transaction_data->credit_account) ? $transaction_data->credit_account->id : '';
                $transactionArray[] = $transaction_data;
            }

        }
        return $transactionArray;
    }

    public function getAllDashboardUsers($request)
    {
        $request['per_page'] = 10;
        $users = $this->repository->getAll($request);
        return response()->json($users);
    }



    public function getDashboardExport($request) : StreamedResponse
    {
        try {
            
            $users = $this->repository->getAll($request, true);
            $fileName = now()->unix().'-'.self::EXPORT_FILE_NAME;
            
            return response()->streamDownload(function () use ($users) { 
                $users->chunk(self::MAX_EXPORT_CHUNK_SIZE, function ($user) {                     
                    echo implode("\r\n", $this->formatExportData($user));
                });
            }, $fileName);

        } catch (Exception $e) {
            report($e);
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_TRANSACTION_REPORT . ' - ' . $message);
            return $this->sendError(self::LOG_TRANSACTION_REPORT, $message);
        }
    }

    public function getDashboardExportBalance($request) : StreamedResponse
    {
        try {

            $pluck = true;
            $currencies = $this->currencyInterface->getAllActive($pluck)->toArray();
            $users = $this->repository->getAll($request, true);
            $fileName = now()->unix().'-'.self::EXPORT_BALANCE_FILE_NAME;
              
             return response()->streamDownload(function () use ($users) { 
                $users->chunk(self::MAX_EXPORT_CHUNK_SIZE, function ($user) {  
                    
                    $userBalances = $user->map(fn($value) => [
                        'account_number' => $value->account_number,
                        'first_name' => $value->first_name,
                        'last_name'  => $value->last_name,
                        'balances' => $value->accounts->mapWithKeys(fn ($account) => [
                            $account->currency => $account->opening_balance
                        ])
                    ]) ;

                    echo implode("\r\n", $this->formatExportBalanceData($userBalances));
                });
            }, $fileName);

        } catch (Exception $e) {
            report($e);
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_TRANSACTION_REPORT . ' - ' . $message);
            return $this->sendError(self::LOG_TRANSACTION_REPORT, $message);
        }
    }

    protected function formatExportData(Collection $collections) : array
    {
        $exportList = $collections->map(fn ($collection) => implode(',', [
            Carbon::parse($collection->created_at)->format('Y-m-d H:i:s'),
            $collection->account_number ,
            $collection->account_type,
            $collection->first_name,
            $collection->last_name,
            $collection->email,
            ($collection->affiliate_details) ? $collection->affiliate_details->parent_code : '',
            $collection->is_dormant ? $this->userInterface::ACCOUNT_DORMANT : $collection->account_status,
            $collection->access_type,
            $this->escapeComma($collection->phone),
            $collection->user_information->date_of_birth,
            $this->escapeComma($collection->user_information->address),
            $this->escapeComma($collection->user_information->city),
            $this->escapeComma($collection->user_information->state),
            $this->escapeComma($collection->user_information->zip),
            $collection->user_information->country_of_residence,
            isset($collection->user_information->account_verified_at) 
                ? Carbon::parse($collection->user_information->account_verified_at)->format('Y-m-d H:i:s') 
                : null,
            isset($collection->user_information->account_closed_at) 
                ? Carbon::parse($collection->user_information->account_closed_at)->format('Y-m-d H:i:s') 
                : null,
            isset($collection->user_information->account_suspended_at) 
                ? Carbon::parse($collection->user_information->account_suspended_at)->format('Y-m-d H:i:s') 
                : null,
            isset($collection->user_information->account_declared_dormant_at) 
                ? Carbon::parse($collection->user_information->account_declared_dormant_at)->format('Y-m-d H:i:s') 
                : null,
            $collection->user_information->language,
            isset($collection->newsletter) ? 'Yes' : 'No',
        ]))->toArray();
        
        array_unshift($exportList, implode(',', self::EXPORT_HEADERS));

        return $exportList;

    }

    protected function formatExportBalanceData($collections) : array
    {
        $exportList = array();

        foreach($collections as $key => $collection) {
            $param = implode(',', [
                $collection['account_number'] ,
                $collection['first_name'],
                $collection['last_name'],
                isset($collection['balances']['USD']) ? $this->escapeComma($collection['balances']['USD']) : '0',
                isset($collection['balances']['EUR']) ? $this->escapeComma($collection['balances']['EUR']) : '0',
                isset($collection['balances']['GBP']) ? $this->escapeComma($collection['balances']['GBP']) : '0',
                isset($collection['balances']['JPY']) ? $this->escapeComma($collection['balances']['JPY']) : '0',
                isset($collection['balances']['PHP']) ? $this->escapeComma($collection['balances']['PHP']) : '0',
                isset($collection['balances']['HKD']) ? $this->escapeComma($collection['balances']['HKD']) : '0'
            ]);

            $exportList[] = $param;
        }

        array_unshift($exportList, implode(',', self::EXPORT_HEADERS_BALANCE));

        return $exportList;
    }

    private function escapeComma($value) : string
    {
        return '"'.$value.'"';
    }
}
