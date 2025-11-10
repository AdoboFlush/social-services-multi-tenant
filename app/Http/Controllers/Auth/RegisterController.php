<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Services\UserService;
use App\Services\AccountService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Http\Requests\RegistrationRequest;
use App\Services\User\UserFacade;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    protected $userService;
    protected $accountService;

    const LOG_REGISTRATION_REQUEST = 'REGISTRATION REQUEST';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService, AccountService $accountService, UserFacade $userFacade)
    {
        $this->middleware('guest');
        $this->userService = $userService;
        $this->accountService = $accountService;
        $this->userFacade = $userFacade;
    }

    public function showRegistrationForm(Request $request, string $id = null)
    {
        return $this->userFacade::showRegistrationForm($request, $id);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param $request
     * @return \App\User
     */
    protected function create($request)
    {
        return $this->userFacade::register($request);
    }

    public function register(RegistrationRequest $request)
    {
        event(new Registered($user = $this->create($request)));
        return redirect('/register/' . base64_encode($user->id));
    }
}
