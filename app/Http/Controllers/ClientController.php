<?php



namespace App\Http\Controllers;

use App\Account;
use App\AccountFee;
use App\CardTransaction;
use App\Document;
use App\Services\DepositCard\DepositCardFacade;
use App\Services\DepositJp\DepositJpFacade;
use App\Services\SEABankDeposit\SEABankDepositFacade;
use App\Services\DixonPay3DS\DixonPay3DSFacade;
use App\Services\WireTransferDeposit\WireTransferDepositFacade;
use App\Services\Withdraw\WithdrawFacade;
use App\Traits\Transact;
use App\Traits\Wire;
use App\Traits\Withdrawal;
use App\Transaction;
use App\WireTransfer;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class ClientController extends BaseController
{
    use Transact;
    use Withdrawal;
    use Wire;

    const LOG_DEPOSIT_REQUEST = 'DEPOSIT LBT REQUEST';
    const LOG_DEPOSIT_VIEW = 'DEPOSIT LBT VIEW';
    const LOG_INTERNAL_TRANSFER = 'LOG INTERNAL TRANSFER REQUEST';
    const LOG_DELETE_BENEFICIARY = 'LOG DELETE BENEFICIARY';
    //2000 EUR
    const MAX_INTERNAL_TRANSFER = 2000;

    private $personal_fee;
    private $business_fee;
    // 15 EUR
    private $maximum_fee = 15;

    public function __construct(
        DepositJpFacade $depositJp,
        DepositCardFacade $depositCard,
        WireTransferDepositFacade $wireTransferDeposit,
        WithdrawFacade $withdrawFacade,
        SEABankDepositFacade $SEABankDeposit,
        DixonPay3DSFacade $DixonPay3DS
    ) {
        $this->SEABankDeposit = $SEABankDeposit;
        $this->depositJp = $depositJp;
        $this->wireTransferDeposit = $wireTransferDeposit;
        $this->depositCard = $depositCard;
        $this->withdrawFacade = $withdrawFacade;
        $this->DixonPay3DS = $DixonPay3DS;
        $this->personal_fee = $this->getFeeByAccountType('personal');
        $this->business_fee = $this->getFeeByAccountType('business');
    }



    public function submit_documents(Request $request)

    {

        if (!$request->isMethod('post')) {

            return view('backend.user_panel.submit_documents');
        } else {



            $validator = Validator::make($request->all(), [

                'nid_passport' => 'required|image',

                'electric_bill' => 'required|image',

            ]);



            if ($validator->fails()) {

                if ($request->ajax()) {

                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                } else {

                    return back()->withErrors($validator)

                        ->withInput();
                }
            }



            // Upload NID / Passport

            if ($request->hasfile('nid_passport')) {

                $file = $request->file('nid_passport');

                $nid_passport = 'Identification_Document_' . time() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path() . "/uploads/documents/", $nid_passport);



                $document = new Document();

                $document->document_name = _lang("Identification Document");

                $document->document = $nid_passport;

                $document->user_id = Auth::user()->id;



                $document->save();
            }



            // Upload Electric Bill

            if ($request->hasfile('electric_bill')) {

                $file = $request->file('electric_bill');

                $electric_bill = 'Address_Verification_' . time() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path() . "/uploads/documents/", $electric_bill);



                $document = new Document();

                $document->document_name = "Address Verification Document";

                $document->document = $electric_bill;

                $document->user_id = Auth::user()->id;



                $document->save();
            }



            //Update User table

            $user = Auth::user();

            $user->document_submitted_at = Carbon::now();

            $user->save();





            return back()->with('document_success', _lang('Thank you for submitting your document(s). Our Customer Support Team will notify you as soon as your document(s) have been reviewed.'));
        }
    }



    /*	Profile Overview */

    public function overview()
    {

        $user = Auth::user();

        return view('backend.user_panel.profile_overview', compact('user'));
    }



    /*	View Account details */

    public function view_account_details(Request $request, $id)
    {

        //$account = Account::where('id',$id)->where('user_id',Auth::id())->first();

        $account = Account::select('accounts.*', DB::raw("((SELECT IFNULL(SUM(amount),0)

                           FROM transactions WHERE dr_cr = 'cr' AND status = 'complete' AND account_id = accounts.id) -

                           (SELECT IFNULL(SUM(amount),0) FROM transactions WHERE dr_cr = 'dr'

                           AND status ='complete' AND account_id = accounts.id)) as balance"))

            ->where('id', $id)

            ->where('user_id', Auth::id())

            ->orderBy('id', 'desc')

            ->first();

        if ($request->ajax()) {

            return view('backend.user_panel.modal.view_account', compact('account', 'id'));
        }
    }



    /*	View Transaction details */

    public function view_transaction(Request $request, $id)
    {

        $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->first();

        if ($request->ajax()) {

            return view('backend.user_panel.modal.view_transaction', compact('transaction', 'id'));
        }
    }



    /** Transfer Between Accounts **/

    public function transfer_between_accounts(Request $request)

    {

        @ini_set('max_execution_time', 0);

        @set_time_limit(0);



        if (!$request->isMethod('post')) {

            return view('backend.user_panel.transfer.transfer_between_accounts');
        } else {

            $validator = Validator::make($request->all(), [

                'amount' => 'required|numeric',

                'debit_account' => 'required',

                'credit_account' => 'required|different:debit_account',

            ]);



            if ($validator->fails()) {

                if ($request->ajax()) {

                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                } else {

                    return back()->withErrors($validator)

                        ->withInput();
                }
            }



            //Generate Fee

            $fee = generate_fee($request->amount, get_option('tba_fee', 0), get_option('tba_fee_type', 'fixed'));



            //Check available balance

            if (get_account_balance($request->debit_account) < ($request->amount + $fee)) {

                return back()->with('error', _lang('Insufficient balance !'));
            }



            DB::beginTransaction();





            /* Status will only apply on credit account */

            $status = 'complete';

            if (get_option('tba_approval') == 'yes') {

                $status = 'pending';
            }



            //Make Debit Transaction

            $debit = new Transaction();

            $debit->user_id = Auth::id();

            $debit->amount = $request->input('amount');

            $debit->account_id = $request->input('debit_account');

            $debit->dr_cr = 'dr';

            $debit->type = 'transfer';

            $debit->status = $status;

            $debit->note = $request->input('note');

            $debit->created_by = Auth::id();

            $debit->updated_by = Auth::id();

            $debit->save();



            //Make fee Transaction

            if ($fee > 0) {

                $fee_debit = new Transaction();

                $fee_debit->user_id = Auth::id();

                $fee_debit->amount = $fee;

                $fee_debit->account_id = $request->input('debit_account');

                $fee_debit->dr_cr = 'dr';

                $fee_debit->type = 'fee';

                $fee_debit->status = $status;

                $fee_debit->parent_id = $debit->id;

                $fee_debit->note = _lang('Transfer Between Account Fee');

                $fee_debit->created_by = Auth::id();

                $fee_debit->updated_by = Auth::id();

                $fee_debit->save();
            }



            //Make Credit Transaction

            $credit = new Transaction();

            $credit->user_id = Auth::id();

            $credit->account_id = $request->input('credit_account');

            $credit->amount = convert_currency(account_currency($debit->account_id), account_currency($credit->account_id), $request->amount);

            $credit->dr_cr = 'cr';

            $credit->type = 'transfer';

            $credit->status = $status;

            $credit->parent_id = $debit->id;

            $credit->note = $request->input('note');

            $credit->created_by = Auth::id();

            $credit->updated_by = Auth::id();

            $credit->save();





            DB::commit();



            if ($credit->id > 0) {

                if ($status == 'complete') {

                    return back()->with('success', _lang('Money Transfer Successfully'));
                } else {

                    return back()->with('success', _lang('Your Transfer is now under review. You will be notified shortly after reviewing by authority.'));
                }
            } else {

                return back()->with('error', _lang('Error Occurred, Please try again !'));
            }
        }
    }


    /** Card Funding Transfer **/

    public function card_funding_transfer(Request $request)
    {

        @ini_set('max_execution_time', 0);

        @set_time_limit(0);



        if (!$request->isMethod('post')) {

            return view('backend.user_panel.transfer.card_funding_transfer');
        } else {

            $validator = Validator::make($request->all(), [

                'amount' => 'required|numeric',

                'debit_account' => 'required',

                'card' => 'required',

            ]);



            if ($validator->fails()) {

                if ($request->ajax()) {

                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                } else {

                    return back()->withErrors($validator)

                        ->withInput();
                }
            }



            //Generate Fee

            $fee = generate_fee($request->amount, get_option('cft_fee', 0), get_option('cft_fee_type', 'fixed'));



            //Check available balance

            if (get_account_balance($request->debit_account) < ($request->amount + $fee)) {

                return back()->with('error', _lang('Insufficient balance !'));
            }



            DB::beginTransaction();





            /* Status will only apply on credit account */

            $status = 'pending';



            //Make Debit Transaction

            $debit = new Transaction();

            $debit->user_id = Auth::id();

            $debit->amount = $request->input('amount');

            $debit->account_id = $request->input('debit_account');

            $debit->dr_cr = 'dr';

            $debit->type = 'card_transfer';

            $debit->status = 'pending';

            $debit->note = $request->input('note');

            $debit->created_by = Auth::id();

            $debit->updated_by = Auth::id();

            $debit->save();





            //Create Wire Transfer Details

            $cardtransaction = new CardTransaction();

            $cardtransaction->card_id = $request->input('card');

            $cardtransaction->dr_cr = 'cr';

            $cardtransaction->amount = convert_currency(account_currency($debit->account_id), card_currency($cardtransaction->card_id), $debit->amount);

            $cardtransaction->note = $request->input('note');

            $cardtransaction->status = 0;

            $cardtransaction->transaction_id = $debit->id;

            $cardtransaction->created_by = Auth::id();

            $cardtransaction->updated_by = Auth::id();



            $cardtransaction->save();





            //Make fee Transaction

            if ($fee > 0) {

                $fee_debit = new Transaction();

                $fee_debit->user_id = Auth::id();

                $fee_debit->amount = $fee;

                $fee_debit->account_id = $request->input('debit_account');

                $fee_debit->dr_cr = 'dr';

                $fee_debit->type = 'fee';

                $fee_debit->status = 'pending';

                $fee_debit->parent_id = $debit->id;

                $fee_debit->note = _lang('Card Funding Transfer Fee');

                $fee_debit->created_by = Auth::id();

                $fee_debit->updated_by = Auth::id();

                $fee_debit->save();
            }



            DB::commit();



            if ($cardtransaction->transaction_id > 0) {

                if ($status == 'complete') {

                    return back()->with('success', _lang('Money Transfer Successfully'));
                } else {

                    return back()->with('success', _lang('Your Card Funding Transfer is processing. You will be notified within 2-3 business days after reviewing by authority. Your Money will be returned back to your debit account if authority reject your transfer.'));
                }
            } else {

                return back()->with('error', _lang('Error Occurred, Please try again !'));
            }
        }
    }





    /** Outgoing Wire Transfer **/

    public function outgoing_wire_transfer(Request $request)

    {

        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        if (!$request->isMethod('post')) {
            return view('backend.user_panel.transfer.outgoing_wire_transfer');
        } else {

            $validator = Validator::make($request->all(), [

                'amount' => 'required|numeric',

                'debit_account' => 'required',

                'currency' => 'required',

                'swift' => 'required|max:50',

                'bank_name' => 'required',

                'bank_country' => 'required',

                'customer_name' => 'required',

                'customer_iban' => 'required|max:50',

                'reference_message' => 'required'

            ]);



            if ($validator->fails()) {

                if ($request->ajax()) {

                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                } else {

                    return back()->withErrors($validator)

                        ->withInput();
                }
            }



            //Generate Fee

            $fee = generate_fee($request->amount, get_option('owt_fee', 0), get_option('owt_fee_type', 'fixed'));



            //Check available balance

            if (get_account_balance($request->debit_account) < ($request->amount + $fee)) {

                return back()->with('error', _lang('Insufficient balance !'));
            }



            DB::beginTransaction();





            /* Status will only apply on credit account */

            $status = 'pending';



            //Make Debit Transaction

            $debit = new Transaction();

            $debit->user_id = Auth::id();

            $debit->amount = $request->input('amount');

            $debit->account_id = $request->input('debit_account');

            $debit->dr_cr = 'dr';

            $debit->type = 'wire_transfer';

            $debit->status = 'pending';

            $debit->note = $request->input('note');

            $debit->created_by = Auth::id();

            $debit->updated_by = Auth::id();

            $debit->save();





            //Create Wire Transfer Details

            $wiretransfer = new WireTransfer();

            $wiretransfer->transaction_id = $debit->id;

            $wiretransfer->swift = $request->input('swift');

            $wiretransfer->bank_name = $request->input('bank_name');

            $wiretransfer->bank_address = $request->input('bank_address');

            $wiretransfer->bank_country = $request->input('bank_country');

            $wiretransfer->rtn = $request->input('rtn');

            $wiretransfer->customer_name = $request->input('customer_name');

            $wiretransfer->customer_address = $request->input('customer_address');

            $wiretransfer->customer_iban = $request->input('customer_iban');

            $wiretransfer->reference_message = $request->input('reference_message');

            $wiretransfer->currency = $request->input('currency');

            $wiretransfer->amount = convert_currency(account_currency($debit->account_id), $wiretransfer->currency, $debit->amount);



            $wiretransfer->save();



            //Make fee Transaction

            if ($fee > 0) {

                $fee_debit = new Transaction();

                $fee_debit->user_id = Auth::id();

                $fee_debit->amount = $fee;

                $fee_debit->account_id = $request->input('debit_account');

                $fee_debit->dr_cr = 'dr';

                $fee_debit->type = 'fee';

                $fee_debit->status = 'pending';

                $fee_debit->parent_id = $debit->id;

                $fee_debit->note = _lang('Outgoing Wire Transfer Fee');

                $fee_debit->created_by = Auth::id();

                $fee_debit->updated_by = Auth::id();

                $fee_debit->save();
            }



            DB::commit();



            if ($wiretransfer->transaction_id > 0) {

                if ($status == 'complete') {

                    return back()->with('wire_success', _lang('Money Transfer Successfully'));
                } else {

                    return back()->with('wire_success', _lang('Your Outgoing Wire Transfer is processing. You will be notified within 2-3 business days after reviewing by authority. Your Money will be returned back to your debit account if authority reject your transfer.'));
                }
            } else {

                return back()->with('error', _lang('Error Occurred, Please try again !'));
            }
        }
    }



    /** Transfer Between Accounts **/

    public function deposit_to_limitz(Request $request)

    {

        @ini_set('max_execution_time', 0);

        @set_time_limit(0);



        if (!$request->isMethod('post')) {

            return view('backend.user_panel.transfer.deposit_to_limitz');
        } else {

            $validator = Validator::make($request->all(), [

                'amount'           => 'required|numeric',

                'limitz_currency' => 'required',

                'debit_account'   => 'required',

                'customer_id'     => 'required',

                'note'               => 'required',

            ]);



            if ($validator->fails()) {

                if ($request->ajax()) {

                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                } else {

                    return back()->withErrors($validator)

                        ->withInput();
                }
            }



            //Check available balance

            if (get_account_balance($request->debit_account) < $request->amount) {

                return back()->with('error', _lang('Insufficient balance !'));
            }



            DB::beginTransaction();





            /* Status will only apply on credit account */

            $status = 'pending';



            //Make Debit Transaction

            $debit = new Transaction();

            $debit->user_id = Auth::id();

            $debit->amount = $request->input('amount');

            $debit->account_id = $request->input('debit_account');

            $debit->dr_cr = 'dr';

            $debit->type = 'transfer';

            $debit->status = $status;

            $debit->note = $request->input('note');

            $debit->created_by = Auth::id();

            $debit->updated_by = Auth::id();

            $debit->save();



            //Send Payment Advice API Request

            $request_amount = convert_currency(account_currency($debit->account_id), $request->limitz_currency, $debit->amount);



            //Login to Limitz API

            $postRequest = array(

                'email'    => get_option('limitz_company_email'),

                'password' => get_option('limitz_company_password')

            );



            $cURLConnection = curl_init('https://uat-api.limtz.com/api/business/login');

            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);

            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);



            $apiResponse = curl_exec($cURLConnection);

            curl_close($cURLConnection);





            $loginResponse = json_decode($apiResponse);



            if ($loginResponse->success == true) {

                $token =  $loginResponse->data->token;
            } else {

                return back()->with('error', _lang('Invalid Company API details !'));
            }



            //Get Bank Details

            $customer_id = $request->customer_id;

            $cURLConnection = curl_init();



            curl_setopt($cURLConnection, CURLOPT_URL, 'https://uat-api.limtz.com/api/funding/' . $customer_id . '/bankdetail');

            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(

                'Authorization: Bearer ' . $token,

            ));

            $bankdetails = curl_exec($cURLConnection);

            curl_close($cURLConnection);



            $response = json_decode($bankdetails);



            if ($response->success == true) {

                $user_bank_id = $response->data[0]->user_bank_id;
            } else {

                return back()->with('error', _lang('Invalid Customer ID !'));
            }



            //Make Payment Advice Request

            $postRequest = array(

                'currency'            => $request->limitz_currency,

                'user_bank_id'         => $user_bank_id,

                'amount'             => $request_amount,

                'payment_reference' => 'Tx1655' . $debit->id,

                'comments'             => $request->note,

            );



            $cURLConnection = curl_init('https://uat-api.limtz.com/api/funding/' . $customer_id . '/advice');

            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);

            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(

                'Authorization: Bearer ' . $token,

            ));



            $apiResponse = curl_exec($cURLConnection);

            curl_close($cURLConnection);





            $response = json_decode($apiResponse);



            $payment_advice_id = '';



            if ($response->success == true) {

                $payment_advice_id =  $response->data->payment_advice_id;
            } else {

                return back()->with('error', $response->message);
            }



            DB::commit();



            if ($payment_advice_id != '') {

                return back()->with('success', 'Your Transfer request (Payment Advide ID: ' . $payment_advice_id . ') has send successfully. You will be notified shortly after reviewing by authority');
            } else {

                return back()->with('error', _lang('Error Occurred, Please try again !'));
            }
        }
    }


    public function SEABankWithdrawalPage(Request $request)
    {
        return $this->SEABankDeposit::withdrawalPage($request);
    }

    public function processSEAWithdrawal(Request $request)
    {
        return $this->SEABankDeposit::processSEAWithdrawal($request);
    }

    public function doSEAWithdrawal(Request $request)
    {
        return $this->SEABankDeposit::doSEAWithdrawal($request);
    }

    /** Deposit to JP Solutions **/

    public function deposit_jp_solution(Request $request)
    {
        return $this->depositJp::create($request);
    }

    public function depositWireTransfer(Request $request)
    {
        return $this->wireTransferDeposit::request($request);
    }

    public function depositWireTransferCompleted(Request $request)
    {
        return $this->wireTransferDeposit::depositWireTransferCompleted($request);
    }

    public function depositSEABankDeposit(Request $request)
    {
        return $this->SEABankDeposit::request($request);
    }

    public function SEADepositSummary(Request $request): JsonResponse
    {
        return response()->json($this->SEABankDeposit::calculateSummary($request));
    }

    public function doSEABankDeposit(Request $request)
    {
        return $this->SEABankDeposit::doSEABankDeposit($request);
    }

    public function doSEABankSession(Request $request)
    {
        return $this->SEABankDeposit::generateBankSession($request);
    }

    public function depositVerification(Request $request)
    {
        return $this->wireTransferDeposit::create($request);
    }

    public function depositDebitCard(Request $request)
    {
        return $this->depositCard::request($request);
    }

    public function dixonPay3DSReturnUrlHandler(Request $request)
    {   
        return $this->DixonPay3DS::returnUrlHandler($request);
    }

    /*	View Transaction details */
    public function view_jp_deposit(Request $request, $response)
    {
        try {
            Log::info(self::LOG_DEPOSIT_VIEW);
            if ($request->ajax()) {
                $response = json_decode($response);
                return view('backend.user_panel.jp_solution.modal.view', compact('response'));
            }
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_DEPOSIT_VIEW . ' - ' . $message);
            return back()->with('error', _lang('Unexpected Error Occurred: ' .  $message));
        }
    }

    /* Referral Commissions */

    public function referral_commissions()
    {

        $data = array();

        $data['referral_commissions'] = \App\ReferralCommission::where('user_id', Auth::id())

            ->where('status', 1)

            ->selectRaw("currency_id, sum(amount) as amount")

            ->groupBy('currency_id')

            ->get();



        return view('backend.user_panel.referral_commissions', $data);
    }



    /* Transfer Referral Commissions to account */

    public function transfer_referral_commissions(Request $request)
    {

        $currency_id = $request->currency_id;



        DB::beginTransaction();

        $commission = \App\ReferralCommission::where('user_id', Auth::id())

            ->where('status', 1)

            ->where('currency_id', $currency_id)

            ->selectRaw("currency_id, sum(amount) as amount")

            ->groupBy('currency_id')

            ->first();



        $credit = new Transaction();

        $credit->user_id = Auth::id();

        $credit->account_id = $request->account_id;

        $credit->amount = convert_currency($commission->currency->name, account_currency($credit->account_id), $commission->amount);

        $credit->dr_cr = 'cr';

        $credit->type = 'revenue';

        $credit->status = 'complete';

        $credit->note = _lang('Referral Commission');

        $credit->created_by = Auth::id();

        $credit->updated_by = Auth::id();

        $credit->save();



        \App\ReferralCommission::where('user_id', Auth::id())

            ->where('status', 1)

            ->where('currency_id', $currency_id)

            ->update(['status' => 0]);



        DB::commit();



        if ($credit->id > 0) {

            return back()->with('success', _lang('Money added to your account Successfully.'));
        } else {

            return back()->with('error', _lang('Error Occurred, Please try again !'));
        }
    }

    public function deleteBeneficiary(Request $request)
    {
        try {
            $wiretransfer = new WireTransfer;
            $wiretransfer->deleteBeneficiaries(Auth::id(), $request->customer_name, $request->bank_name, $request->bank_branch_name, $request->method);
            return response()->json([
                'status' => 1,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_DELETE_BENEFICIARY . ' - ' . $message);
            return response()->json(
                [
                    'status' => 0,
                    'message' => _lang('Unexpected error: ' . $message)
                ]
            );
        }
    }

    /** Wire Transfer **/
    public function wire_transfer(Request $request)
    {
        return $this->withdrawFacade::withdrawViaWireTransfer($request);
    }

    /*
    * @param string $account_type
    *
    * This method gets fee per account type
    *
    * return float
    */
    private function getFeeByAccountType(string $account_type): float
    {
        return AccountFee::where('account_type', $account_type)->first()->fee / 100.00;
    }
}
