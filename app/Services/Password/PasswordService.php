<?php

namespace App\Services\Password;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;

use App\Mail\User\CreatePasswordRequestMailer;
use App\Mail\VerificationSuccessMail;
use App\Mail\Security\CreationSuccessMail;
use App\Mail\Security\UpdateSuccessMail;
use App\Mail\Security\ResetSuccessMail;

use App\Services\BaseService;
use App\Repositories\User\UserInterface;
use App\Repositories\PasswordResets\PasswordResetsInterface;
use App\Repositories\Security\SecurityInterface;

use Auth;
use DB;
use Validator;

class PasswordService extends BaseService
{
    const REDIRECT_TO = '/password/expired';
    const CREATE = 'create';
    const UPDATE = 'update';

    use ResetsPasswords;

    protected $userInterface;

    public function __construct(UserInterface $userInterface, PasswordResetsInterface $passwordResetsInterface, SecurityInterface $securityInterface) {
        $this->userInterface = $userInterface;
        $this->passwordResetsInterface = $passwordResetsInterface;
        $this->securityInterface = $securityInterface;
    }

    public function create($token,$expiration_date, $request){
        $password_reset = $this->passwordResetsInterface->findByEmail(base64_decode($request->email));
        $email = $request->email;
        if (strtotime(date('Y-m-d H:i:s')) > $expiration_date) {
            return view('auth.passwords.expired',compact('email'));
        }
        if (empty($password_reset) ||
            !Hash::check($token, $password_reset->token)
        ) {
            return abort(404);
        }
        
        $user = $this->userInterface->getByAccountNumberOrEmail(base64_decode($email));
        if(!empty($user->password))
            return redirect('login');
        return view('auth.passwords.create', compact('token','email'));
    }

    public function resendTokenTo($email){
        $user = $this->userInterface->getByAccountNumberOrEmail(base64_decode($email));
        $user->generateTwoFactorCode(1440);
        $token = app('auth.password.broker')->createToken($user);
        $expiration_date = strtotime("+24 hours");
        $user->url = url('/password/create/' . $token ."/".$expiration_date."?email=" . base64_encode($user->email)."&language=".$user->user_information->language);
        Mail::to($user->email)->send(new CreatePasswordRequestMailer($user));
        return redirect(self::REDIRECT_TO . '/' . $email);
    }

    public function reset($request){

        $request->validate($this->rules(), $this->validationErrorMessages());

        $user = $this->userInterface->getByAccountNumberOrEmail(base64_decode($request->email));
        if ($user->verification_code_expires_at > Carbon::now()) {
            if ($request->verification_code == $user->verification_code) {
                // Here we will attempt to reset the user's password. If it is successful we
                // will update the password on an actual user model and persist it to the
                // database. Otherwise we will parse the error and return the response.
                if(empty($user->password)){
                    $this->resetPassword($user, $request->password);
                    $user->new_email = $user->email;
                    Mail::to($user->email)->send(new VerificationSuccessMail($user));
                    unset($user->new_email);
                    Auth::logout();
                    return view('auth.verified_success');
                } else {
                    $this->resetPassword($user, $request->password);
                    Auth::logout();
                    return back()->with('status', 'success')->withInput();
                }
            }
            return back()->withErrors([_lang('Invalid verification code!')]);
        }
        return back()->with('error', _lang('Invalid verification code!'));
    }

    public function securityPassword($request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'bail|required|min:6|confirmed'
        ], ['required'    => 'Please fill out this field']);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'error' => _lang($validator->messages()->getMessages()["password"][0]),
            ]);
        }

        return response()->json([
            'status' => 1
        ]);
    }

    public function createSecurityPassword($request)
    {
        $param = array(
            "user_id" => Auth::user()->id,
            "password" =>  Hash::make($request->password),
            "status" => 1
        );
        $action = Auth::user()->security->password ? self::UPDATE : self::CREATE;
        $security = $this->securityInterface->create($param);
        if($security){
            if($action == self::UPDATE){
                Mail::to(Auth::user()->email)->send(new UpdateSuccessMail(Auth::user()));
                $message = _lang('Your master password has been reset. A notification email has been sent to {email}',['email' => Auth::user()->email]);
            } else {
                Mail::to(Auth::user()->email)->send(new CreationSuccessMail(Auth::user()));
                $message = _lang('Your master password has been created. A notification email has been sent to {email}',['email' => Auth::user()->email]);
            }
            return redirect('user/security_settings')->with('message',$message);
        }
        return view('backend.security.index')->withErrors(['An Unexpected Error Occurred']);
    }

    public function editSecurityPassword($request, $user_id)
    {
        $param = array(
            "status" => $request->status
        );
        $security = $this->securityInterface->update($user_id,$param,false);
        if($security){
            $user = $this->userInterface->get($user_id);
            $log = $request->status ? "Enabled Master Password" : "Disabled Master Password";
            activity('Master Password')
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->log($log);
            return back()->with('success', _lang('Updated Successfully'));
        }
        return back()->withErrors(['An Unexpected Error Occurred']);
    }

    public function confirmSecurityPassword($request)
    {
        if (Hash::check($request->password, Auth::user()->security->password)) {
            return back()->with('success',_lang("You can now use this service."))->withCookie('confirmed','confirmed');
        }
        return back()->withCookie('confirmed',0)->withErrors([_lang('Invalid master password.')]);
    }

    public function resetSecurityPassword($user_id)
    {
        $tempPassword = Str::random(8);
        $user = $this->userInterface->get($user_id);
        $user->code = $tempPassword;

        $param = array(
            "user_id" => $user_id,
            "password" =>  Hash::make($tempPassword) ,
        );
        $security = $this->securityInterface->create($param);

        if($security){
            activity('Master Password')
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->log("Reset Master Password");
            session(['forcedLanguage' => $user->user_information->language]);
            Mail::to($user->email)->send(new ResetSuccessMail($user));
            session()->forget('forcedLanguage');
            $message = _lang("Master Password has been reset. Please use ".$tempPassword." to access the Oriental Wallet services. An email has been sent to ".$user->email);
            return response()->json([
                'status' => 1,
                'message' => $message,
            ]);
        }
        return response()->json([
            'status' => 0,
            'message' => _lang("An error occurred."),
        ]);
    }
    
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));
        $this->guard()->login($user);
    }


    protected function rules()
    {
        return [
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ];
    }
}
