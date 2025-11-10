<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Mail\User\ResetPasswordRequestMailer;

use App\Services\Password\PasswordFacade;

use DB;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(PasswordFacade $password)
    {
        $this->password = $password;
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $user = User::where('email',$request->email)->first();
        if (empty($user)) {
            return back()->withErrors(_lang('Please provide a valid email address.'))->withInput();
        }
        $user->generateTwoFactorCode();
        $token = app('auth.password.broker')->createToken($user);
        $expiration_date = strtotime("+1 hours");
        $user->url = url('/password/change_password/' . $token ."/".$expiration_date."?email=" . base64_encode($user->email));
        Mail::to($user->email)->send(new ResetPasswordRequestMailer($user));

        return back()->with('status', _lang('Please check your email. We sent a verification link.'))->withInput();
    }

    public function change_password($token, $expiration_date, Request $request)
    {

        $password_reset = DB::table('password_resets')
            ->where('email', base64_decode($request->email))
            ->first();

        if (
            (strtotime(date('Y-m-d H:i:s')) > $expiration_date) || 
            (empty($password_reset) || !Hash::check($token, $password_reset->token))
        ) {
            $is_expired = 'Password reset link is already expired, please  go to the reset password form again.';
            return view('auth.passwords.reset_password', compact('is_expired'));
        }

        $user = User::where('email',$request->email)->first();
        $email = $request->email;
        return view('auth.passwords.reset_password', compact('user','token','email'));
    }

    public function createPassword($token, $expiration_date, Request $request)
    {
        return $this->password::create($token,$expiration_date, $request);
    }

    public function resendToken($email)
    {
        return $this->password::resendTokenTo($email);
    }

    public function viewExpiredPage($email)
    {
        return view('auth.passwords.expired',compact('email'));
    }

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'g-recaptcha-response' => 'required|captcha'
            ],
            [
                'g-recaptcha-response.required'=> _lang('Please verify the captcha.')
            ]
        );
    }

}
