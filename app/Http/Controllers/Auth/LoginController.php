<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\User\UserFacade;
use App\Services\User\UserService;
use App\Setting;
use App\User;
use App\UserSession;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class LoginController extends Controller
{
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

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

    protected function authenticated(Request $request, User $user): RedirectResponse
    {   
        if ($user->user_type === User::TAGGER) {
            return redirect(route("voter.tagging.view", [], false));
        }

        if ($user->user_type === User::PAYMASTER) {
            return redirect(route("paymaster.voter_assistance.events.index", [], false));
        }

        if ($user->user_type === User::WATCHER) {
            return redirect(route("poll.guest.watcher.dashboard", [], false));
        }

        if ($user->is_admin) {
            return redirect(route("admin.dashboard", [], false));
        } else {
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
            return redirect($this->redirectTo)
                ->withCookie("language", $language)
                ->withCookie('confirmed', 0)
                ->with('is_dormant', $user->is_dormant);
        }
    }

    protected function credentials(Request $request)
    {
        return [
            'email' => $request->{$this->username()},
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
