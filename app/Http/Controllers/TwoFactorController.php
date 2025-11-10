<?php
namespace App\Http\Controllers;

use App\Mail\TwoFactor\TwoFactorCreateSecurityPasswordMailer;
use App\Mail\TwoFactor\TwoFactorInternalTransferMailer;
use App\Mail\TwoFactor\TwoFactorUpdateSecurityPasswordMailer;
use App\Mail\TwoFactor\TwoFactorForceUpdatePasswordMailer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;

use App\Mail\TwoFactor\TwoFactorWithdrawalJpMailer;
use App\Mail\TwoFactor\TwoFactorPaymentRequestMailer;
use App\Mail\TwoFactor\TwoFactorWithdrawalViaWireMailer;
use App\Mail\TwoFactor\TwoFactorCardTopUpMailer;
use App\Mail\TwoFactor\TwoFactorWithdrawalSEAMailer;
use App\TwoFaCode;
use App\Traits\TwoFactor;
use Auth;
use DB;

class TwoFactorController extends BaseController
{
	use TwoFactor;

	const LOG_CREATE_2FA = "LOG CREATE TWO FACTOR: ";
	const LOG_CREATE_2FA_ERROR = 'LOG CREATE TWO FACTOR ERROR:';

    const ERROR_STATUS = 401;

    private const WITHDRAWAL_SEA = 'withdrawal_sea';
    private const WITHDRAWAL_JP = 'withdrawal_jp';
    private const INTERNAL_TRANSFER = 'internal_transfer';
    private const PAYMENT_REQUEST = 'payment_request';
    private const WIRE_TRANSFER = 'wire_transfer';
    private const CARD_TOPUP = 'card_topup';
    private const FORCE_CHANGE_PASSWORD = 'force_change_password';
    private const SECURITY_PASSWORD = 'security_password';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
    	try {
    		$user = Auth::user();

            if ($request->ajax() ){
        		Log::info(self::LOG_CREATE_2FA . $user->id );
        		if ($request->two_fa_type) {
    				$code = $this->createCode($user->id, $request->two_fa_type);

    				$data = new \stdClass();
    				$data->user_id = $user->id;
    				$data->first_name = $user->first_name;
    				$data->last_name = $user->last_name;
                    $data->account_number = $user->account_number;
    				$data->code = $code;

                    if ($user->account_status != 'Verified' && ($request->two_fa_type == 'withdrawal_jp' || $request->two_fa_type == 'wire_transfer')){
                        return $this->sendError(['message' => 'Please verify your account first before making any withdrawals', 'http_code' => 404], '');
                    }

                    if($request->two_fa_type === self::WITHDRAWAL_SEA) {
                        Mail::to($user->email)->send(new TwoFactorWithdrawalSEAMailer($data));
                        $action = url("user/withdrawal_sea/verified", );
                    }

    				if ($request->two_fa_type == self::WITHDRAWAL_JP) {
    					Mail::to($user->email)->send(new TwoFactorWithdrawalJpMailer($data));
                        $action = url('user/withdrawal_jp');
    				}
                    if ($request->two_fa_type == self::INTERNAL_TRANSFER) {
                        Mail::to($user->email)->send(new TwoFactorInternalTransferMailer($data));
                        $action = url('user/store-internal-transfer');
                    }
                    if ($request->two_fa_type == self::PAYMENT_REQUEST) {
                        Mail::to($user->email)->send(new TwoFactorPaymentRequestMailer($data));
                        $action = url('user/payment_requests/approve');
                    }
                    if ($request->two_fa_type == self::WIRE_TRANSFER) {
                        Mail::to($user->email)->send(new TwoFactorWithdrawalViaWireMailer($data));
                        $action = url('user/wire_transfer');
                    }
                    if ($request->two_fa_type == self::CARD_TOPUP) {
                        Mail::to($user->email)->send(new TwoFactorCardTopUpMailer($data));
                        $action = url('user/card-topup');
                    }
                    if ($request->two_fa_type == self::FORCE_CHANGE_PASSWORD) {
                        Mail::to($user->email)->send(new TwoFactorForceUpdatePasswordMailer($data));
                        $action = url('user/update-change-password');
                    }
                    if ($request->two_fa_type == self::SECURITY_PASSWORD) {
                        if(Auth::user()->security->password){
                            Mail::to($user->email)->send(new TwoFactorUpdateSecurityPasswordMailer($data));
                        } else {
                            Mail::to($user->email)->send(new TwoFactorCreateSecurityPasswordMailer($data));
                        }
                        $action = url('user/security_settings/create');
                    }

    				$request = $request->except('two_fa_type');
        		}

        		return view('backend.two_factor.create', compact('request', 'action'));
            }

    	} catch (\Exception $e) {
    		$message = $this->getErrorMessage($e);
            Log::error(self::LOG_CREATE_2FA_ERROR . ' - ' . $message);
            return response()->json(['message' => _lang('Unexpected error: ' . $message)],  self::ERROR_STATUS);
    	}
    }

    public function verify(Request $request)
    {
        try {
            $user = Auth::user();
            if ($request->code) {
                if ($this->validateCode($user->id, $request->code)) {
                    return $this->sendResponse(true, 'Valid Code');
                } else {
                    return $this->sendError(['message' => 'Invalid Code', 'http_code' => 404], '');
                }
            }
        } catch (\Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_CREATE_2FA_ERROR . ' - ' . $message);
            return $this->sendError(['message' => 'Unexpected error: ' . $message, 'http_code' => 404], '');
        }
    }
}
