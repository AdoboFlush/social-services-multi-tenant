<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberLoginRequest;
use App\Repositories\UserRepository;
use App\Services\User\UserFacade;
use App\Services\User\UserService;
use App\Setting;
use App\User;
use App\UserSession;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class MemberLoginController extends Controller
{
    use RedirectsUsers, ThrottlesLogins;
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $userRepository;
    protected $user;
    protected $userService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        UserFacade $userFacade,
        UserService $userService
    ) {
        $this->middleware('guest')->except('logout');
        $this->userRepository = $userRepository;
        $this->user = $userFacade;
        $this->userService = $userService;

        $action = Route::getCurrentRoute()->getActionName();
        if (strpos($action, 'merchantLogin') !== false) {
            Auth::logout();
        }
    }

    protected function guard()
    {
        return Auth::guard();
    }

    public function username()
    {
        return 'account_number';
    }

    public function showLoginForm()
    {
        return view('guest.login');
    }

    public function login(MemberLoginRequest $request)
    {
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

     /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );        
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect(route('guest.landing', [], false));
    }


    protected function authenticated(Request $request, User $user): RedirectResponse
    {   
        if ($user->is_member) {
            $language = Auth::user()->user_information->language;
            $this->userService->updateLastLogin($user);
            if (Setting::where('name','isMaintenance')->first()->value !== Setting::MAINTENANCE_ACTIVE) {
                $this->userService->addUserSession($request, $user->id, UserSession::METHOD_OWL);
            }
            if ($request->has('redirectTo')) {
                return redirect($request->redirectTo)
                    ->withCookie("language", $language)
                    ->with('is_dormant', $user->is_dormant);
            }
            return redirect(route('guest.profile', [], false))
                ->withCookie("language", $language)
                ->withCookie('confirmed', 0)
                ->with('is_dormant', $user->is_dormant);
        }

        return redirect()->route('guest.logout', [], false);
    }

    protected function credentials(Request $request)
    {
        return [
            $this->username() => $request->{$this->username()},
            'password' => $request->password,
            'status' => 1
        ];
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        // Load user from database
        $user = User::where($this->username(), $request->{$this->username()})->first();
        // Check if user was successfully loaded, that the password matches
        // and active is not 1. If so, override the default error message.
        if ($user && Hash::check($request->password, $user->password) && $user->status != 1) {
            $errors = [$this->username() => _lang('Your account is not active !')];
        }

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    public function merchantLogin(Request $request)
    {
        return $this->user::merchantLogin($request);
    }

    public function merchantLoginAttempt(Request $request)
    {
        return $this->user::merchantLoginAttempt($request);
    }
}
