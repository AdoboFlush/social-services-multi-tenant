<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationMail;
use App\Services\User\UserFacade;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Utilities\Overrider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationSuccessMail;
use Illuminate\Support\Facades\URL;
use Auth;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/email/verified';

    private $userFacade;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserFacade $userFacade)
    {
		//Overrider::load("Settings");
        //$this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->userFacade = $userFacade;
    }

    /**
     * Mark the authenticated user’s email address as verified.
     *
     * @param Request $request
     * @return void
     */
    public function verify(Request $request)
    {
        activity()->disableLogging();
        $user = User::findOrFail($request->route('id'));
        if (isset($request->email) && $request->email != null) {
            $user->update(['email' => base64_decode($request->email)]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        return redirect($this->redirectTo . '/' . base64_encode($user->id) . '/' . $request->email);
    }

    /**
     * Landing page after email veriification
     *
     * @param string $id
     * @return void
     */
    public function verified_success(string $id, string $email = null)
    {
        return $this->userFacade::verifyUserEmail($id,$email);
    }

    /**
     * Verify Change Email
     *
     * @param Request $request
     * @return void
     */
    public function change_email(Request $request)
    {
        $user = User::findOrFail($request->route('id'));
        $user->update(['email' => $request->route('email')]);

        if ($user->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        return redirect($this->redirectTo . '/' . base64_encode($user->id));
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addHour(24), ['id' => $request->user()->id]
        );
        $request->user()->verification_url = $verifyUrl;

        Mail::to($request->user()->email)->send(new RegistrationMail($request->user()));

        return back()->with('resent', true);
    }
}
