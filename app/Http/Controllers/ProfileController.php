<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Http\Requests\Password;
use App\Mail\User\EmailRequestedMailer;
use App\Mail\User\PasswordRequestedMailer;
use App\Mail\User\UpdateInformationRequestMailer;
use App\Mail\User\VerifyEmailRequestMailer;
use App\Mail\User\VerifyPasswordRequestMailer;
use App\Services\Profile\ProfileFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use App\User;
use Hash;
use Auth;
use Illuminate\Support\Carbon;
use App\Http\Requests\PasswordRequest;

class ProfileController extends Controller
{

    const LOG_TWO_FACTOR_AUTHENTICATION_REQUEST = 'TWO FACTOR AUTHENTICATION REQUEST';
    const LOG_UPDATE_PASSSWORD_REQUEST = 'UPDATE PASSWORD REQUEST';
    const INVALID_CODE = 'Invalid 2FA Code';

    protected $profileFacade;

    public function __construct(ProfileFacade $profileFacade){
        $this->profileFacade = $profileFacade;
    }

    public function edit()
    {
        $profile = Auth::user();
        return view('backend.profile.profile_edit',compact('profile'));
    }


    public function update(Request $request)
    {
        return $this->profileFacade::update($request);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update_password(PasswordRequest $request)
    {
        try {
            $request = $request->all();
            $user = User::find(Auth::User()->id);

            if (Hash::check($request['oldpassword'], $user->password)){
                //$user->generateTwoFactorCode();
                //Mail::to(Auth::user()->email)->send(new PasswordRequestedMailer($user));
                // $message = _lang("Your action requires 2-step verification. Please check your e-mail with verification code that was sent to {email}.",['email' => Auth::user()->email]);
                $user->password = bcrypt($request['password']);
                $user->save();
                $message = "Password successfully changed!";

                return response()->json(
                    [
                        'result' => 'success',
                        'title' => 'Change Password',
                        //'load-verification-modal-password' => true,
                        'verified' => true,
                        'message' => $message,
                        'data' => $request
                    ]
                );
            }
            return response()->json(
                [
                    'result' => 'error',
                    'load_change_password_modal' => true,
                    'message' => _lang('The old password is incorrect.'),
                ]
            );
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_UPDATE_PASSSWORD_REQUEST . ' - ' . $message);
            return response()->json(
                [
                    'result' => 'error',
                    'message' => $message,
                    'load_change_password_modal' => true
                ]
            );
        }
    }

    /**
     * Show referral link.
     *
     * @return \Illuminate\Http\Response
     */
    public function referral_link()
    {
        //$user = User::whereRaw("md5(id) = ?",['eccbc87e4b5ce2fe28308fd9f2a7baf3'])->first();
        return view('backend.profile.referral_link');
    }

    /**
     * Generate Verification Code and send it to the user
     *
     * @param EmailRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update_email(EmailRequest $request)
    {

        $request = $request->all();
        $user = User::find(Auth::User()->id);
        $user->generateTwoFactorCode();
        Mail::to(Auth::user()->email)->send(new EmailRequestedMailer($user));

        $message = _lang("Your action requires 2-step verification. Please check your e-mail with verification code that was sent to {email}.",['email' => Auth::user()->email]);
        return response()->json(
            [
                'result' => 'success',
                'load-verification-modal-email' => true,
                'message' => $message,
                'data' => $request
            ]
        );
    }

    /**
     * Show the form for verification_code the specified resource.
     */
    public function update_verification(Request $request)
    {
        try {

            $request->validate([
            'verification_code' => 'integer|required',
            ]);

            $request = $request->all();

            if (isset($request['btn']) && $request['btn'] == 'resend') {
                $user = User::find(Auth::User()->id);
                $user->generateTwoFactorCode();
                Mail::to(Auth::user()->email)->send(new EmailRequestedMailer($user));

                $message = _lang("Your action requires 2-step verification. Please check your e-mail with verification code that was sent to {email}.",['email' => Auth::user()->email]);
                return response()->json(
                    [
                        'result' => 'success',
                        'message' => $message,
                        'two_step_verification' => true
                    ]
                );
            }

            if (Auth::user()->verification_code_expires_at > Carbon::now()) {
                $user = User::find(Auth::User()->id);
                if ($request['verification_code'] == $user->verification_code) {
                    if (isset($request['new_email']) && $request['new_email'] != '') {
                        $verifyUrl = URL::temporarySignedRoute(
                            'verification.verify', Carbon::now()->addHour(24),
                            [
                                'id' => Auth::user()->id,
                                'email' => base64_encode($request['new_email']),
                            ]
                        );

                        $emailRequest = new \stdClass();
                        $emailRequest->url = $verifyUrl;
                        $emailRequest->first_name = Auth::user()->first_name;
                        $emailRequest->last_name = Auth::user()->last_name;
                        $emailRequest->account_number = Auth::user()->account_number;

                       Mail::to($request['new_email'])
                           ->send(new VerifyEmailRequestMailer($emailRequest));

                        return response()->json(
                            [
                                "result" => "success",
                                "title" => _lang("Change Email"),
                                "message" => _lang("An email with verification URL has been sent to your new email address and valid for 24 hours only. Please check the link to completely change your email address. If you haven't complete the verification within 24 hours, please repeat the same process in order to receive a new verification URL. Your account will be log out in 10 seconds."),
                                "two_step_verification" => true,
                                "verified" => true
                            ]
                        );
                    } else {
                        User::where('id',Auth::user()->id)->update(['password' => Hash::make($request['password'])]);

                        $emailRequest = new \stdClass();
                        $emailRequest->first_name = Auth::user()->first_name;
                        $emailRequest->last_name = Auth::user()->last_name;
                        $emailRequest->account_number = Auth::user()->account_number;

                        Mail::to(Auth::user()->email)
                           ->send(new VerifyPasswordRequestMailer($emailRequest));

                        return response()->json(
                            [
                                'result' => 'success',
                                'message' => _lang('Change of Password is completed, you will be automatically logout after 10 seconds. Please login again using your new password.'),
                                'title' => _lang('Change Password'),
                                'two_step_verification' => true,
                                'verified' => true
                            ]
                        );
                    }

                }
                return response()->json(
                    [
                        'result' => 'error',
                        'message' => _lang(self::INVALID_CODE),
                        'two_step_verification' => true
                    ]
                );
            }

            return response()->json(
                [
                    'result' => 'error',
                    'message' => 'Your Two Factor Code is already expired, click here to 
                    <input type="submit" class="link-button" value="resend" /> a new code.',
                    'two_step_verification' => true
                ]
            );

        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_TWO_FACTOR_AUTHENTICATION_REQUEST . ' - ' . $message);
            return response()->json(
                [
                    'result' => 'error',
                    'message' => $message,
                ]
            );
        }
    }

    public function update_information_request()
    {
        return view('backend.profile.modal.update_information_request');
    }

    public function send_request(Request $request)
    {
        return $this->profileFacade::requestForChangeNameOrDateOfBirth($request);
    }
}
