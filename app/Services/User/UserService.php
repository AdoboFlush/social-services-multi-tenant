<?php

namespace App\Services\User;

use App\Account;
use App\Http\Requests\CsvRequests\BulkUserRequests;
use App\Http\Requests\RequireChangePasswordRequest;
use App\Http\Requests\UpdateUserDocumentStatusRequest;
use App\Http\Requests\UserCardNumberRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\RegistrationRequest;
use App\KycStatus;
use App\Mail\RegistrationMail;
use App\Mail\User\BusinessAccountVerifiedMailer;
use App\Mail\User\CreatePasswordRequestMailer;
use App\Mail\User\PersonalAccountVerifiedMailer;
use App\Mail\VerificationSuccessMail;
use App\Repositories\Account\AccountInterface;
use App\Repositories\Country\CountryInterface;
use App\Repositories\Currency\CurrencyInterface;
use App\Repositories\User\UserInterface;
use App\Services\AccountService;
use App\Services\BaseService;
use App\Services\MemberCode\MemberCodeFacade;
use App\Traits\Signature;
use App\Transaction;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client as GuzzleClient;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Redirect;
use Validator;
use Cookie;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserService extends BaseService
{
    use Signature;

    protected $userInterface;
    protected $accountInterface;
    protected $currencyInterface;
    protected $countryInterface;
    protected $accountService;
    protected $memberCodeFacade;

    private const LOG_REGISTRATION_REQUEST = 'REGISTRATION REQUEST';
    private const LOG_USER_UPDATE = 'USER UPDATE';
    private const LOG_USER_EDIT = 'USER EDIT';
    private const LOG_USER_CHANGE_PASS = 'LOG CHANGE PASS:';

    private const PRIORITY_CURRENCY = 'JPY';
    private const DEFAULT_LANGUAGE = "English";
    private const API_LOGIN_URL = '/api/auth/login';
    private const ACCOUNT_UNVERIFIED = 'Unverified';
    private const BUSINESS = 'business';
    private const DORMANCY_CACHE_EXPIRATION = 30;
    private const DORMANCY_CACHE_VALUE = 1;
    private const MAX_EXPORT_CHUNK_SIZE = 10000;

    private const INCORRECT_USER_BALANCE_FILE = 'owl_incorrect_balance_users';

    public function __construct(
        UserInterface $userInterface,
        AccountService $accountService,
        AccountInterface $accountInterface,
        CurrencyInterface $currencyInterface,
        CountryInterface $countryInterface,
        MemberCodeFacade $memberCodeFacade
    ) {
        $this->userInterface = $userInterface;
        $this->accountService = $accountService;
        $this->accountInterface = $accountInterface;
        $this->currencyInterface = $currencyInterface;
        $this->countryInterface = $countryInterface;
        $this->memberCodeFacade = $memberCodeFacade;
    }

    //@TODO: Will delete once passed in production
    public function forceDormant(string $id, int $days)
    {
        $user = $this->userInterface->getUserByAccountNumber($id);
        if ($user) {
            if (count($user->accounts)) {
                foreach ($user->accounts as $account) {
                    $account->updated_at = Carbon::now()->subDays($days);
                    $account->save();
                }
            } else {
                $user->created_at = Carbon::now()->subDays($days);
                $user->save();
            }
            if (!Cache::has('DormancyCheckerInstance')) {
                Cache::put('DormancyCheckerInstance', self::DORMANCY_CACHE_VALUE, self::DORMANCY_CACHE_EXPIRATION);
                Artisan::call("check:dormancy", array('user_id' => $user->id));
                return "Dormancy checker successfully executed";
            } else {
                return "Dormancy checker is still running. Please wait for " . self::DORMANCY_CACHE_EXPIRATION . " seconds";
            }
        }
        return "User Not Found";
    }

    public function forceCheckMaintenance(string $id): string
    {
        if (App::environment('staging')) {
            if (!empty($id)) {
                $user = $this->userInterface->getUserByAccountNumber($id);
                if ($user) {
                    Artisan::call("check:maintenance", array('user_id' => $user->id));
                    return "Maintenance fee cron job successfully executed only for account number: $id";
                }
                return "User Not Found";
            } else {
                Artisan::call("check:maintenance");
                return "Maintenance fee cron job successfully executed";
            }
        }
        return "Not Allowed";
    }

    /**
     * @return Redirect | View
     */
    public function showRegistrationForm(Request $request, string $id = null)
    {
        if (get_option('allow_singup', 'yes') != 'yes') {
            return redirect('login');
        } else {
            $language = (null !== $request->cookie('language')) ? ucfirst($request->cookie('language')) : 'English';
            $user = [];
            if ($id != null) {
                $user = $this->userInterface->get(base64_decode($id));
            }
            $countries = $this->countryInterface->getAllActive();
            return view('auth.register', compact('user', 'countries', 'language'));
        }
    }

    public function register(RegistrationRequest $request): User
    {
        $user = self::store($request);
        return $user;
    }

    public function getAllUsers(string $account_status = null)
    {
        $title = _lang('User List');
        $users = $this->userInterface->getAllUsers($account_status);
        return view('backend.user.list', compact('users', 'title'));
    }

    public function individualCreate(Request $request)
    {
        $userRequest = new UserRequest();

        $rules = $userRequest->rules();
        $messages = $userRequest->messages();
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect('admin/users/create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        $user = self::store($request);

        activity('User List')
            ->performedOn($user)
            ->withProperties(array(
                "name" => $user->first_name . " " . $user->last_name,
                "account_number" => $user->account_number
            ))->log('Register');

        if (! $request->ajax()) {
            return redirect('admin/users/create')->with('success', _lang('Saved Successfully'));
        } else {
            $user->account_type = ucwords($user->account_type);
            $user->user_type = ucwords($user->user_type);
            $user->status = $user->status == 1
                ? status(_lang('Active'), 'success') : status(_lang('In-Active'), 'danger');
            $user->account_status = $user->account_status == 'Verified'
                ? status(_lang('Verified'), 'success') : status(_lang('Unverified'), 'danger');
            return response()->json([
                'result' => 'success',
                'action' => 'store',
                'message' => _lang('Saved Successfully'),
                'data' => $user
            ]);
        }
    }

    public function bulkCreate(Request $request)
    {
        try {
            $csv = new BulkUserRequests();
            $file = $request->file("csv_file");
            $fileName = 'users_' . time() . '.' . $file->getClientOriginalExtension();
            $originalFile = fopen($file, "r");

            activity()->disableLogging();
            $newUsers = [];
            while (($column = fgetcsv($originalFile, 10000, ",")) !== false) {
                if (!strtotime($column[0])) {
                    continue;
                }
                $record = $this->setCsvParams($column, $csv);
                $error = $csv->validate($record);
                if ($error) {
                    return response()->json([
                        'status' => 1,
                        'message' => _lang('Unexpected Error Occurred.'),
                    ]);
                };
                $request = new Request($record);
                $user = $this->store($request);
                $user->generateTwoFactorCode(1440);
                $token = app('auth.password.broker')->createToken($user);
                $expiration_date = strtotime("+24 hours");
                $user->url = url("/password/create/" . $token . "/" . $expiration_date .
                    "?email=" . base64_encode($user->email) . "&language=" . $user->user_information->language);
                array_push($newUsers, $user->first_name . " " . $user->last_name . " " . $user->account_number);
                session(['forcedLanguage' => $user->user_information->language]);
                Mail::to($user->email)->send(new CreatePasswordRequestMailer($user));
                session()->forget('forcedLanguage');
            }
            activity()->enableLogging();
            activity('User List')
                ->withProperties($newUsers)
                ->log('Bulk Register');
        } catch (\Swift_TransportException $e) {
            activity()->enableLogging();
            activity('User List')
                ->withProperties($newUsers)
                ->log('Bulk Register');
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_REGISTRATION_REQUEST . ' - ' . $message);
            return response()->json([
                'status' => 0,
                'message' => _lang('Unexpected Error Occurred: ' .  $message),
            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => 'Successfully registered users.',
        ]);
    }

    public function reviewCsv(Request $request)
    {
        try {
            $csv = new BulkUserRequests();
            $file = $request->file("csv_file");
            $originalFile = fopen($file, "r");
            $users = array();
            while (($column = fgetcsv($originalFile, 10000, ",")) !== false) {
                if (!strtotime($column[$csv::DATE])) {
                    continue;
                }
                $record = $this->setCsvParams($column, $csv);
                $record["errors"] = $csv->validate($record);
                foreach ($users as $user) {
                    if (in_array($record['email'], $user)) {
                        array_push($record['errors'], _lang('Duplicate entry of email'));
                    }
                }
                array_push($users, $record);
            }
            return view('backend.user.modal.review', ['data' => $users]);
        } catch (\ErrorException $e) {
            return response()->json([
                'status' => 0,
                'message' => _lang('Invalid CSV Format')
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $param = array(
                'account_number' => $this->accountService->generateAccountNumber($request->account_type),
                'first_name' => strtoupper($request->first_name),
                'last_name' => strtoupper($request->last_name),
                'email' => $request->email,
                'password' => isset($request->password) && $request->password ? Hash::make($request->password) : "",
                'phone' => isset($request->phone) && !empty($request->phone) ? $request->phone : null,
                'user_type' => 'user',
                'status' => 1,
                'mid' => $this->createMid(),
                'date_of_birth' => $request->date_of_birth,
                'country_of_residence' => $request->country_of_residence,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'language' => $request->language ? $request->language : self::DEFAULT_LANGUAGE
            );

            if (Auth::check() && Auth::user()->user_type == "admin") {
                $param['created_by'] = Auth::id();
                $param['updated_by'] = Auth::id();
            }

            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $file_name = "profile_" . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/profile/'), $file_name);
                $param['profile_picture'] = $file_name;
            }
            if ($request->has('ref')) {
                /* @note
                 * referral user id must also include the affiliate code
                 * teehee
                 */
                $user = $this->userInterface->getReferrer($request->ref);
                if ($user && $user->status && $user->account_type == "business") {
                    $param['refer_user_id'] = $user->id;
                }
            }
            $user = $this->userInterface->register($param);

            $this->userInterface->update($user->id, ['email_verified_at' => now()]);
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_REGISTRATION_REQUEST . ' - ' . $message);
            return back()->with('error', _lang('Unexpected Error Occurred: ' .  $message));
        }
    }

    /**
     * @return View | Redirect
     */
    public function edit(Request $request, string $account_number)
    {
        try {
            $currencies = $this->currencyInterface->getAllActive();
            $countries = $this->countryInterface->getAllActive();
            $user = $this->userInterface->getByAccountNumber($account_number);
            if (!$user) {
                $user = $this->userInterface->get($account_number);
            }
            if ($user && isset($user->account_number)) {
                $accounts = $this->currencyInterface->getCurrenciesAndAccounts($user->id);
                $data['type'] = $request->type;
                $data['status'] = $request->status;
                $data['date1'] = $request->date1;
                $data['date2'] = $request->date2;
                $data['currencies'] = $currencies;
                $data['currency'] = $request->currency;
                $data['viewOnly'] = (isset($request->view) && $request->view == 1) ? $request->view : '';
                return view('backend.user.edit', compact('user', 'accounts', 'data', 'currencies', 'countries'));
            } else {
                return redirect("admin/users")->with('error', _lang("No user found with account number: " . $account_number));
            }
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_USER_EDIT . ' - ' . $message);
            return redirect("admin/users")->with('error', _lang('Unexpected Error Occurred: ' .  $message));
        }
    }

    public function update(Request $request, int $id)
    {
        try {

            activity()->disableLogging();
            $validator = $this->validateUserUpdate($request, $id);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                } else {
                    return redirect()->route('users.edit', $id)
                        ->withErrors($validator)
                        ->withInput();
                }
            }

            DB::beginTransaction();

            $user = $this->userInterface->get($id);

            $has_been_verfied = $request->account_status === $this->userInterface::ACCOUNT_VERIFIED;

            if (($has_been_verfied
                    && $user->account_status !== $this->userInterface::ACCOUNT_VERIFIED)
                || ($user->is_dormant && $has_been_verfied)
            ) {
                $this->userInterface->appendKYCStatusToRemarks($user, $request->account_status);
            }

            $param = $request->all();

            if ($request->has('password')) {
                $param['password'] = Hash::make($request->password);
            }

            $param['is_admin_account'] = $request->has("is_admin_account") ? 1 : 0;
            $param['is_dormant'] = $request->account_status === $this->userInterface::ACCOUNT_DORMANT ? 1 : 0;

            if ($request->account_status !== $user->account_status) {
                if ($param['is_dormant']) {
                    $force_update_status_date = $param['is_dormant'] !== $user->is_dormant;
                    unset($param['account_status']);
                } else {
                    $force_update_status_date = $request->account_status !== $this->userInterface::ACCOUNT_VERIFIED;
                }
                $this->userInterface->updateAccountStatusDate(
                    $user,
                    $request->account_status,
                    $force_update_status_date
                );
            }

            if ($request->account_status !== $this->userInterface::ACCOUNT_DORMANT && $user->is_dormant) {
                $this->updateAccountsUpdatedAt($user);
            }
            $param['user_type'] = 'user';
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $file_name = "profile_" . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/profile/'), $file_name);
                $param['profile_picture'] = $file_name;
            }
            $param['updated_by'] = Auth::id();

            if (empty($user->mid)) {
                $param['mid'] = $this->createMid();
            }

            $param["referral_switch"] = $request->has("referral_switch") ? 1 : 0;

            $redirect = $this->isAccountTypeChanged($request, $user);
            $original = array_merge(
                $user->getOriginal(),
                $user->user_information->getOriginal()
            );
            $user = $this->userInterface->update($id, $param);
            $this->logUserUpdate($param, $user, $original);

            //Prefix Output
            $user->account_type = ucwords($user->account_type);
            $user->user_type = ucwords($user->user_type);
            $user->status = $user->status == 1
                ? status(_lang('Active'), 'success') : status(_lang('In-Active'), 'danger');
            $user->account_status = $user->account_status == 'Verified'
                ? status(_lang('Verified'), 'success') : status(_lang('Unverified'), 'danger');

            DB::commit();
            if (!$request->ajax()) {
                if (isset($request->view) && $request->view == 1) {
                    if ($redirect) {
                        return redirect()->route('users.edit', $user->account_number)
                            ->with('success', _lang('Updated Successfully'));
                    }
                    return back()->with('success', _lang('Updated Successfully'));
                }
                if ($redirect) {
                    return redirect()->route('users.edit', $user->account_number)
                        ->with('success', _lang('Updated Successfully'));
                }
                return back()->with('success', _lang('Updated Successfully'));
            } else {
                return response()->json([
                    'result' => 'success',
                    'action' => 'update',
                    'message' => _lang('Updated Successfully'),
                    'data' => $user
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_USER_UPDATE . ' - ' . $message);
            return back()->with('error', _lang('Unexpected Error Occurred: ' .  $message));
        }
    }

    public function switchLanguage(string $to)
    {
        if (Auth::check()) {
            $result = $this->userInterface->changeLanguageOf(Auth::user()->id, $to);
            if ($result) {
                session(['forcedLanguage' => $result->user_information->language]);
                $message = _lang('Language Successfully Updated');
                session()->forget('forcedLanguage');
                return back()->with('success', $message)->withCookie('language', $to);
            }
            return back()->with('error', _lang('Unable to change language'));
        }
        return back()->with('success', _lang('Language Successfully Updated'))->withCookie('language', $to);
    }

    public function searchUser(Request $request)
    {
        try {
            if ($request->ajax()) {
                $user = $this->userInterface->getByAccountNumberOrEmail(
                    $request->user_account
                );

                if (!empty($user)) {
                    if ($user->id == Auth::user()->id) {
                        return response()->json(
                            [
                                'message' => _lang('Request invalid, you cannot transfer to your own account.'),
                            ]
                        );
                    }

                    if ($user->account_status == $this->userInterface::ACCOUNT_CLOSED) {
                        return response()->json(
                            [
                                'success' => false,
                                'message' => _lang('The destination account is unavailable.'),
                            ]
                        );
                    }

                    $userAccounts = $this->accountInterface->getAccountsByUserId($user->id)
                        ->where('status', 1)
                        ->where('opening_balance', '>', 0);

                    return response()->json(
                        [
                            'success' => true,
                            'message' => _lang('User Account') . ' - ' . $user->first_name . ' ' .
                                $user->last_name  . ' ' .  $user->account_number,
                            'data' => $userAccounts,
                        ],
                        200
                    );
                }

                return response()->json(
                    [
                        'message' => _lang('User Account does not exist'),
                    ]
                );
            }
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            return response()->json(
                [
                    'message' => $message,
                ]
            );
        }
    }

    public function searchUserBy(string $account_number)
    {
        $user = $this->userInterface->getByAccountNumber($account_number);
        $message = _lang("Invalid account number");
        if ($user) {
            return response()->json([
                'user' => $user,
                'success' => true
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => $message,
        ]);
    }

    public function viewDocuments()
    {
        $statuses = $this->userInterface::ACCOUNT_STATUSES;
        return view('backend.user.documents', compact('statuses'));
    }

    public function viewDocumentsById(int $id)
    {
        $user = $this->userInterface->get($id);
        $documents = $user->documents;
        return view('backend.user.view_documents', compact('documents', 'user'));
    }

    public function getDocuments(Request $request): JsonResponse
    {
        $users = $this->userInterface->getUsersByUserType($request);
        return response()->json($users);
    }

    public function updateKycById(Request $request, int $id)
    {
        $user = $this->userInterface->get($id);

        $payload = $request->all();
        $message = _lang('Account updated successfully');
        if ($request->kyc_status != $user->kyc_status) {
            $payload['kyc_status'] = $request->kyc_status;
            $payload['kyc_status_updated_at'] = Carbon::now();
        }
        $user = $this->userInterface->update($id, $payload);
        if ($user) {
            $changes = $user->getChanges();
            if (!$changes) {
                $message = _lang('No update has been made');
            }
            return back()->with('varified_success', $message);
        }
        return back()->with('varified_fail', _lang('No update has been made'));
    }

    public function verifyUser(int $id)
    {
        $payload = array(
            "account_status" => $this->userInterface::ACCOUNT_VERIFIED,
            "kyc_status" => KycStatus::VERIFIED,
            "is_dormant" => 0,
        );

        $user = $this->userInterface->updateKycStatus($id, $payload);

        $changes = $user->getChanges();

        if (
            isset($changes['kyc_status']) ||
            isset($changes['account_status']) ||
            isset($changes['is_dormant'])
        ) {
            /**
             * update the verified date forcefully
             */
            $force_update_status_date = !$user->user_information->account_verified_at;

            $this->userInterface->updateAccountStatusDate(
                $user,
                $this->userInterface::ACCOUNT_VERIFIED,
                $force_update_status_date
            );
            $this->userInterface->appendKYCStatusToRemarks($user, $this->userInterface::ACCOUNT_VERIFIED);

            if (isset($changes['is_dormant'])) {
                $this->updateAccountsUpdatedAt($user);
            }

            session(['forcedLanguage' => $user->user_information->language]);
            if (lcfirst($user->account_type) == "personal") {
                Mail::to($user->email)->send(new PersonalAccountVerifiedMailer($user));
            } else {
                Mail::to($user->email)->send(new BusinessAccountVerifiedMailer($user));
            }
            session()->forget('forcedLanguage');
            return back()->with('varified_success', _lang('Account Verified successfully'));
        } else {
            return back()->with('varified_fail', _lang('No update has been made'));
        }
    }

    public function updateCardNumber(Request $request, int $id): RedirectResponse
    {
        $payload = [
            'card_number' => $request->card_number,
            'card_register_at' => $request->card_register_at,
            'card_register_status' => $request->card_register_status,
            'card_application_exemption' => $request->card_application_exemption
        ];

        $user = $this->userInterface->update($id, $payload);

        if ($user) {
            return back()->with('success', _lang('Successfully updated user card details'));
        }
        return back()->with('error', _lang('Unable to update user card details'));
    }

    public function removeCardNumber(int $id)
    {
        $payload = [
            'card_number' => '',
            'card_register_at' => null
        ];

        $user = $this->userInterface->update($id, $payload);

        if ($user) {
            return back()->with('card_number_success', _lang('Card Number removed successfully.'));
        }
        return back()->with('card_number_fail   ', _lang('Unable to remove user card number.'));
    }

    public function filterUserDocumentsAndDetailsBy(Request $request)
    {
        $users = $this->userInterface->filterUserDocumentsAndDetailsBy($request);
        $inputs = $request->has("reset") ? [] : $request->input();

        $all = [];
        foreach ($users as $user) {
            $all[] = $user->id;
        }

        $all = json_encode($all);

        return view('backend.user.documents')->with(compact('users', 'all'))->with($inputs);
    }

    public function merchantLogin(Request $request)
    {
        Auth::logout();

        $error = '';
        $redirectUrl = '';
        if (!$request->has('receive_url')) {
            $error = 'No receive url found.';
        }

        $lang = (isset($request->lang) && $request->lang == 'jp') ? 'japanese' : 'english';
        $receiveUrl = $request->input('receive_url');

        Cookie::queue(cookie('language', $lang));

        if ($lang == 'japanese' && !session()->has('reloadlang')) {
            session(['reloadlang' => '1']);
            session(['forcedLanguage' => $lang]);
            header("Refresh:0"); //needs to reload so that cookie would appear
        } else {
            session()->forget('reloadlang');
            session()->forget('forcedLanguage');
            return view('layouts.merchant_login', compact('error', 'receiveUrl', 'lang'))->withCookie('language', $lang);
        }
    }

    public function merchantLoginAttempt(Request $request)
    {
        try {
            $httpClient = new GuzzleClient([
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);
            $response = $httpClient->post(env('APP_URL') . self::API_LOGIN_URL, [
                'form_params' => [
                    'email' => $request->email,
                    'password' => $request->password,
                    'origin_user_agent' => $request->header('User-Agent'),
                    'origin_ip_address' => $request->ip(),
                ]
            ]);

            $contents = $response->getBody()->getContents();
            $contents = json_decode($contents);

            if (!$contents->result) {
                $error = $contents->message;
                return redirect()->back()->withErrors($error)->withInput();
            }

            $qry_param = http_build_query(['authenticated_token' => $contents->access_token, 'account_number' => $contents->user->account_number]);
            $receive = $request->input('receive_url') . '?' . $qry_param;
            return Redirect::to($receive);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            return response()->json(
                [
                    'message' => $message,
                ]
            );
        }
    }

    public function submitChangePassword(Request $request)
    {
        $validator = $this->validateForceChangePassword($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        return back()->withInput()->with('generate_two_factor', true);
    }
    public function updateForceChangePassword(Request $request)
    {
        try {
            Log::info(self::LOG_USER_CHANGE_PASS);
            DB::beginTransaction();
            $user = Auth::user();
            $payload['password'] = Hash::make($request['password']);
            $payload['change_password'] = 1;
            $pass = $this->userInterface->update($user->id, $payload);
            DB::commit();

            return redirect('/dashboard');
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOG_USER_CHANGE_PASS . ' - ' . $message);
            return back()->with('error', _lang('Unexpected Error Occurred: ' .  $message));
        }
    }
    public function resendVerificationEmail(Request $request)
    {
        try {
            $user = $this->userInterface->get($request->user_id);
            $this->sendEmailVerification($user, $request);

            activity('User Account Detail')
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->log("Resent Verification Link");
            return response()->json([
                'status' => true,
                'message' => 'Verification link has been resent to ' . $user->first_name . ' ' . $user->last_name
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Unexpected error occurred']);
        }
    }

    public function verifyUserEmail(string $id = null, string $email = null)
    {
        $user = $this->userInterface->get(base64_decode($id));
        $user->new_email = $email;
        session(['forcedLanguage' => $user->user_information->language]);
        Mail::to($user->email)->send(new VerificationSuccessMail($user));
        session()->forget('forcedLanguage');
        Auth::logout();
        return view('auth.verified_success');
    }

    public function updateKycStatus(Request $request)
    {
        $validator = $this->validateUpdateUserDocumentStatusRequest($request);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        foreach ($request->update_checked as $checked_id) {
            $this->userInterface->update($checked_id, [
                'kyc_status' => $request->update_status,
                'kyc_status_updated_at' => Carbon::now()
            ]);
        }

        return back()->with('success', _lang('User KYC Status Updated Successfully'));
    }
    private function validateUpdateUserDocumentStatusRequest($request)
    {
        $updateUserDocumentStatusRequest = new UpdateUserDocumentStatusRequest();
        $rules = $updateUserDocumentStatusRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function setCsvParams(array $column, BulkUserRequests $csv)
    {
        return array(
            "date" => isset($column[$csv::DATE]) && !empty($column[$csv::DATE]) ? $column[$csv::DATE] : "",
            "email" => isset($column[$csv::EMAIL]) && !empty($column[$csv::EMAIL]) ? $column[$csv::EMAIL] : "",
            "first_name" => isset($column[$csv::FIRST_NAME]) && !empty($column[$csv::FIRST_NAME])
                ? $column[$csv::FIRST_NAME] : "",
            "last_name" => isset($column[$csv::LAST_NAME]) && !empty($column[$csv::LAST_NAME])
                ? $column[$csv::LAST_NAME] : "",
            "date_of_birth" => isset($column[$csv::DATE_OF_BIRTH]) && !empty($column[$csv::DATE_OF_BIRTH])
                ? $column[$csv::DATE_OF_BIRTH] : "",
            "account_type" => isset($column[$csv::ACCOUNT_TYPE]) && !empty($column[$csv::ACCOUNT_TYPE])
                ? $column[$csv::ACCOUNT_TYPE] : "",
            "phone" => isset($column[$csv::PHONE]) && !empty($column[$csv::PHONE]) ? $column[$csv::PHONE] : "",
            "address" => isset($column[$csv::ADDRESS]) && !empty($column[$csv::ADDRESS]) ? $column[$csv::ADDRESS] : "",
            "city" => isset($column[$csv::CITY]) && !empty($column[$csv::CITY]) ? $column[$csv::CITY] : "",
            "state" => isset($column[$csv::STATE]) && !empty($column[$csv::STATE]) ? $column[$csv::STATE] : "",
            "zip" => isset($column[$csv::ZIP]) && !empty($column[$csv::ZIP]) ? $column[$csv::ZIP] : "",
            "country_of_residence" => isset($column[$csv::COUNTRY]) && !empty($column[$csv::COUNTRY])
                ? $column[$csv::COUNTRY] : "",
            "language" => isset($column[$csv::LANGUAGE]) && !empty($column[$csv::LANGUAGE])
                ? ucwords($column[$csv::LANGUAGE]) : self::DEFAULT_LANGUAGE
        );
    }

    private function validateForceChangePassword(Request $request)
    {
        $requireChangePasswordRequest = new RequireChangePasswordRequest();
        $messages = [
            'required'    =>  _lang("The :attribute is required."),
            'min'    => _lang("The :attribute must have at least 8 characters."),
            'password.same' => _lang('The password confirmation does not match.')
        ];
        $rules = $requireChangePasswordRequest->rules();
        return Validator::make($request->all(), $rules, $messages);
    }

    private function validateCardTopUp(Request $request)
    {
        $userCardNumberRequest = new UserCardNumberRequest();
        $rules = $userCardNumberRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function validateUserUpdate(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'nullable|max:20|min:6|confirmed',
            'status' => 'required',
            'city' => 'nullable|max:100',
            'state' => 'nullable|max:100',
            'zip' => 'nullable|max:20',
            'profile_picture' => 'nullable|image|max:5120',
            'is_included_on_dormancy' => 'nullable|max:2',
        ]);
        return $validator;
    }

    private function logUserUpdate(array $param, User $user, array $original): void
    {
        $old = $new = array();
        foreach ($original as $key => $val) {
            if (isset($param[$key]) && $param[$key] != $val) {
                switch ($key) {
                    case "status":
                        $old[$key] = $val ? "Active" : "Inactive";
                        $new[$key] = $param[$key] ? "Active" : "Inactive";
                        break;
                    case "profile_picture":
                        $old[$key] = $val;
                        $new[$key] = $user->profile_picture;
                        break;
                    case "is_dormant":
                        unset($old[$key]);
                        unset($new[$key]);
                        $old["account_status"] = $val ?
                            $this->userInterface::ACCOUNT_DORMANT :
                            $original['account_status'];
                        $new["account_status"] = $val ?
                            $original['account_status'] :
                            $this->userInterface::ACCOUNT_DORMANT;
                        break;
                    case "updated_by":
                        $old[$key] = $val ? $this->userInterface->get($val)->full_name : "";
                        $new[$key] = isset($param[$key]) && $param[$key] ?
                            $this->userInterface->get($param[$key])->full_name : "";
                        break;
                    default:
                        $old[$key] = $val;
                        $new[$key] = $param[$key];
                        break;
                }
            }
        }

        $log = array(
            'old' => $old,
            'attributes' => $new
        );

        activity()->enableLogging();
        activity("User Account Detail")
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties($log)
            ->log('updated');
    }

    private function isAccountTypeChanged(Request $request, User $user)
    {
        if ($user->getOriginal()['account_type'] != $request['account_type']) {
            return true;
        }
        return false;
    }

    private function generateAgentCode(User $user)
    {
        $name = substr($user->first_name, 0, 5);
        $nameLength = strlen($name);
        if ($nameLength < 5) {
            $name = $name . substr($user->last_name, 0, 5 - $nameLength);
        }
        return $name . mt_rand(10000, 99999);
    }

    private function updateAccountsUpdatedAt(User $user): void
    {
        if (isset($user->accounts) && count($user->accounts) > 0) {
            foreach ($user->accounts as $account) {
                $account->updated_at = Carbon::now();
                $account->save();
            }
        } else {
            $account_details = [
                'user_id' => $user->id,
                'currency' => self::PRIORITY_CURRENCY,
                'status' => 1,
                'opening_balance' => 0
            ];
            $this->accountInterface->create($account_details);
        }
    }

    public function updateLastLogin(User $user): bool
    {
        return $this->userInterface->updateLastLogin($user);
    }

    private function sendEmailVerification(User $user, Request $request)
    {
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addHour(24),
            ['id' => $user->id]
        );
        $user->verification_url = $verifyUrl;

        $lang = $request->cookie('language');

        if (empty($lang) || $request->has('email_notif')) { //email_notif means its from the admin page
            $lang = $user->user_information->language;
        }
        Log::info("Lang " . $lang);
        session(['forcedLanguage' => $lang]);
        Mail::to($user->email)->send(new RegistrationMail($user));
        session()->forget('forcedLanguage');
    }

    public function userSessions(): View
    {
        return view('backend.risk_management.usersession');
    }

    public function userSessionsHistory(Request $request): JsonResponse
    {
        $request['per_page'] = isset($request->filter['rows']) ? $request->filter['rows'] : 10;
        $sessions = $this->userInterface->getUsersSessions($request);
        return response()->json($sessions);
    }

    public function addUserSession(Request $request, int $user_id, string $method)
    {
        $parameter = [
            'user_id' => $user_id,
            'user_agent' => ($request->has('origin_user_agent')) ? $request->origin_user_agent : $request->header('User-Agent'),
            'ip_address' => ($request->has('origin_ip_address')) ? $request->origin_ip_address : $request->ip(),
            'method' => $method
        ];
        $this->userInterface->addUserSession($parameter);
    }

    public function generateIncorrectBalanceUserCSV(Request $request): StreamedResponse
    {

        try {
            $users = $this->userInterface->getIncorrectBalanceUsers($request);
            $users = collect($users);
            $fileName = now()->unix() . '-' . self::INCORRECT_USER_BALANCE_FILE;
            if ($request->has('offset')) {
                $fileName .= "-offset-" . $request->offset;
            }
            if ($request->has('limit')) {
                $fileName .= "-limit-" . $request->limit;
            }
            $fileName .= ".csv";
            return response()->streamDownload(function () use ($users) {
                echo implode(",", [
                    'user_id',
                    'account_number',
                    'name',
                    'transaction_number',
                    'dr_cr',
                    'currency',
                    'transaction_current_balance',
                    'transaction_expected_balance',
                    'latest_account_opening_balance'
                ]) . "\r\n";
                foreach ($users as $user) {
                    echo implode(",", $user) . "\r\n";
                }
            }, $fileName);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error('Incorrect Balance Users CSV - ' . $message);
        }
    }


    public function generateIncorrectBalanceUserCSVBackend(int $offset, int $limit): void
    {
        try {

            $request = new Request();
            $request->offset = $offset;
            if ($limit > 0) {
                $request->limit = $limit;
            }
            $users = $this->userInterface->getIncorrectBalanceUsers($request);
            $users = collect($users);
            $fileName = now()->unix() . '-' . self::INCORRECT_USER_BALANCE_FILE;
            if ($request->has('offset')) {
                $fileName .= "-offset-" . $request->offset;
            }
            if ($request->has('limit')) {
                $fileName .= "-limit-" . $request->limit;
            }
            $fileName .= ".csv";
            $csvContent = "user_id,account_number,name,transaction_number,dr_cr,currency,transaction_current_balance,transaction_expected_balance, latest_account_opening_balance\r\n";
            foreach ($users as $key => $user) {
                $csvContent .= implode(",", $user) . "\r\n";
                unset($user[$key]); // to free up buffer memory
            }

            $message = "Incorrect Balance Users CSV created - $fileName";
            echo $message;
            Log::info($message);
            Storage::disk('public')->put($fileName, $csvContent);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error('Incorrect Balance Users CSV - ' . $message);
        }
    }

    public function createMemberUser(Request $request)
    {
        try {
            DB::beginTransaction();

            $code_with_member = $this->memberCodeFacade::getMemberByCode($request->member_code);

            $member = $code_with_member->member;

            User::create([
                'account_number' => $member->account_number,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'password' => Hash::make($request->password),
                'status' => '1',
                'user_type' => 'member',
            ]);

            $code_with_member->active = 1;
            $code_with_member->save();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        return false;
    }

    public function resetMemberPassword(Request $request)
    {
        try {

            $user = $this->userInterface->getUserByAccountNumber($request->account_number);

            if ($user) {
                DB::beginTransaction();

                $user->password = Hash::make($request->password);
                $user->save();

                DB::commit();

                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
