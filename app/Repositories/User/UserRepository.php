<?php

namespace App\Repositories\User;

use App\Session;
use App\Transaction;
use App\User;
use App\UserSession;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRepository implements UserInterface
{
    private $model;

    public function __construct(
        User $model,
        Session $session,
        UserSession $userSession
    ) {
        $this->model = $model;
        $this->session = $session;
        $this->userSession = $userSession;
    }

    public function create($request)
    {
        $request = $this->convertToUppercase($request);
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $user = $this->model->find($id);
        if ($user) {
            $request = $this->convertToUppercase($request);
            $user->update($request);
            $user->user_information->update($request);
            return $user;
        }
        return false;
    }

    public function updateKycStatus(int $id, array $request): User
    {
        $user = $this->model->find($id);
        if ($user) {
            if (!$user->kyc_status_updated_at) {
                $request["kyc_status_updated_at"] = Carbon::now();
            }
            $user->update($request);
            return $user;
        }
        throw new Exception('Unable to find user.');
    }

    public function delete($id)
    {
        $user = $this->model->find($id);
        if ($user) {
            $user->delete();
            return $user;
        }
        return false;
    }

    public function get($id, $with = '')
    {
        if ($with != '') {
            return $this->model->with($with)->find($id);
        }
        return $this->model->find($id);
    }

    public function getReferrer($id)
    {
        return $this->model->whereRaw("md5(id) = ?", [$id])->first();
    }

    public function getAll()
    {
        $model = $this->model;
        return $model->get();
    }

    public function register(array $params)
    {
        $user = $this->model->create($params);
        $user->user_information()->create($params);
        return $user;
    }

    public function getByAccountNumber($accountNumber)
    {
        return $this->model->where('account_number', $accountNumber)->first();
    }

    public function getByAccountNumberOrEmail($emailOrAccountNumber)
    {
        return $this->model->where('email', $emailOrAccountNumber)
            ->orWhere('account_number', $emailOrAccountNumber)
            ->first();
    }

    public function filterUserDocumentsAndDetailsBy($request)
    {
        $user =  $this->model->where('user_type', 'user');
        $user->has('documents')->get();
        if ($request->has("reset")) {
            return $user->get();
        }
        $user->where(function ($query) use ($request) {
            if ($request->has('verified')) {
                $query->where('account_status', "verified");
            }
            if ($request->has('unverified')) {
                $query->orWhere('account_status', "unverified");
            }
            if ($request->has('dormant')) {
                $query->orWhere('account_status', "dormant");
            }
            if ($request->has('suspended')) {
                $query->orWhere('account_status', "suspended");
            }
            if ($request->has('closed')) {
                $query->orWhere('account_status', "closed");
            }
            if ($request->has('wcheck')) {
                $query->orWhere('kyc_status', "wcheck");
            }
            if ($request->has('unreviewed')) {
                $query->orWhere('kyc_status', "unreviewed");
            }
            if ($request->has('approved')) {
                $query->orWhere('kyc_status', "approved");
            }
            if ($request->has('rejected')) {
                $query->orWhere('kyc_status', "rejected");
            }
            if ($request->has('pending')) {
                $query->orWhere('kyc_status', "pending");
            }
            if ($request->has('card_approved')) {
                $query->orWhere('kyc_status', "card-approved");
            }
            if ($request->has('card_rejected')) {
                $query->orWhere('kyc_status', "card-rejected");
            }
            if ($request->has('card_pending')) {
                $query->orWhere('kyc_status', "card-pending");
            }
        });
        return $user->get();
    }

    public function changeLanguageOf($id, $to)
    {
        $information = $this->model->find($id);
        $information->user_information->language = $to;
        $information->user_information->save();
        return $information;
    }

    public function getUsersByUserType(Request $request): Object
    {
        return $this->model
            ->where('user_type', 'user')
            ->when(
                $request->has('filter') && array_key_exists('search', $request->filter),
                function (Builder $searchQuery) use ($request) {
                    $search = $request->filter['search'];
                    $searchQuery->where(function ($searchSubQuery) use ($search) {
                        $searchSubQuery->where('first_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                            ->orWhere(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', '%' . $search . '%')
                            ->orWhere('account_number', 'LIKE', '%' . $search . '%')
                            ->orWhere('email', 'LIKE', '%' . $search . '%')
                            ->orWhere('kyc_status', 'LIKE', '%' . $search . '%');
                    });
                }
            )
            ->when(
                $request->has('filter') && array_key_exists('account_status', $request->filter),
                function (Builder $accountStatusSubQuery) use ($request) {
                    $account_statuses = explode(",", $request->filter['account_status']);
                    $accountStatusSubQuery->where(function ($subQuery) use ($account_statuses) {
                        foreach ($account_statuses as $account_status) {
                            if ($account_status == self::ACCOUNT_DORMANT) {
                                $subQuery->orWhere("is_dormant", 1);
                            } else {
                                $subQuery->orWhere(function ($accountStatusQuery) use ($account_status) {
                                    $accountStatusQuery->where("account_status", $account_status)->where("is_dormant", 0);
                                });
                            }
                        }
                    });
                }
            )
            ->when(
                $request->has('filter') && array_key_exists('kyc_status', $request->filter),
                function (Builder $accountStatusSubQuery) use ($request) {
                    $account_statuses = explode(",", $request->filter['kyc_status']);
                    $accountStatusSubQuery->where(function ($subQuery) use ($account_statuses) {
                        foreach ($account_statuses as $account_status) {
                            $subQuery->orWhere("kyc_status", $account_status);
                        }
                    });
                }
            )
            ->where(function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $subQuery->where('account_status', '<>', self::ACCOUNT_UNVERIFIED)
                        ->where('document_submitted_at', '<>', null);
                })
                    ->orWhere(function (Builder $subQueryForVerifiedWithoutDocuments) {
                        $subQueryForVerifiedWithoutDocuments
                            ->where('account_status', self::ACCOUNT_VERIFIED)
                            ->where('document_submitted_at', null);
                    })
                    ->orWhere(function (Builder $subQueryForUnverifiedWithDocuments) {
                        $subQueryForUnverifiedWithDocuments
                            ->where('account_status', self::ACCOUNT_UNVERIFIED)
                            ->where('document_submitted_at', '<>', null);
                    });
            })
            ->orderBy('document_submitted_at', 'desc')
            ->paginate(10, ['*'], 'page', $request->page);
    }

    public function getUserByAccountNumber($account_number)
    {
        return $this->model->where('account_number', $account_number)->first();
    }

    public function getAllUsers($account_status = null)
    {
        $user =  $this->model->where('user_type', 'user');
        if (isset($account_status) && $account_status) {
            $user->where(function ($query) use ($account_status) {
                if ($account_status == "Verified") {
                    $query->where('account_status', $account_status)->where('is_dormant', '!=', 1);
                } else {
                    $query->where('account_status', $account_status)->where('is_dormant', '!=', 1);
                    $query->orWhere('account_status', null);
                }
            });
        }
        return $user->get();
    }
    public function liftDormancy($user)
    {
        if (!($user instanceof User)) {
            $user = self::get($user);
        }
        if ($user && $user->is_dormant) {
            $user->is_dormant = 0;
            $user->save();
            $message = "\n" . Carbon::now()->format('Y-m-d H:i:s A');
            $message .= "\nChanged from Dormant to " . $user->account_status;
            $user->user_information->remarks = $message . "\n" . $user->user_information->remarks;
            $user->user_information->save();
            return $user;
        }
        return false;
    }

    public function convertNamesToUpperCase()
    {
        $users = $this->model->get();
        foreach ($users as $user) {
            $user->first_name = strtoupper($user->first_name);
            $user->last_name = strtoupper($user->last_name);
            $user->save();
        }
    }

    public function getAdminUsers()
    {
        return $this->model->where('user_type', 'admin')->orderBy('first_name', 'ASC')->get();
    }

    private function convertToUppercase($request)
    {
        if (isset($request['first_name']) && $request['first_name']) {
            $request['first_name'] = strtoupper($request['first_name']);
        }
        if (isset($request['last_name']) && $request['last_name']) {
            $request['last_name'] = strtoupper($request['last_name']);
        }
        return $request;
    }

    public function appendKYCStatusToRemarks(User $user, string $status): bool
    {
        $actor = Auth::user();
        $date_time = Carbon::now()->format('Y-m-d H:i:s A');
        $template = $date_time . " " . $actor->full_name . "\r\nAccount " . $status;
        $user->kyc_remarks = $user->kyc_remarks
            ? $template . "\r\n\r\n" . $user->kyc_remarks
            : $template;

        return $user->save();
    }

    public function updateAccountStatusDate(User $user, string $status, bool $force_update = false): bool
    {
        $user_information = $user->user_information;
        $date_time = Carbon::now()->toDateTimeString();
        $field = null;

        switch ($status) {
            case self::ACCOUNT_DORMANT:
                $field = 'account_declared_dormant_at';
                break;
            case self::ACCOUNT_VERIFIED:
                $field = 'account_verified_at';
                break;

            case self::ACCOUNT_SUSPENDED:
                $field = 'account_suspended_at';
                break;

            case self::ACCOUNT_CLOSED:
                $field = 'account_closed_at';
                break;

            case self::ACCOUNT_UNVERIFIED:
                /** do nothing */
                break;
            default:
                throw new Exception('Unknown account ' . $status . ' status received');
        }

        /**
         * if field is null or function should update forcefully
         */
        if ($field) {
            if (is_null($user_information->{$field}) || $force_update) {
                $user_information->{$field} = $date_time;
            }
        }

        return $user_information->save();
    }

    public function updateLastLogin(User $user): bool
    {
        $user->user_information->last_login_at = Carbon::now()->toDateTimeString();

        return $user->user_information->save();
    }

    public function memberSessions(): Collection
    {
        return $this->model
            ->whereHas('session', function (Builder $query) {
                $query->where('user_id', '<>', null);
            })
            ->where('account_type', '<>', User::ADMIN)
            ->get();
    }

    public function getUsersSessions(Request $request = null): Paginator
    {
        $search_in_user = ['account_number', 'account_name'];
        $session = $this->userSession->with('user')->orderBy('created_at', 'desc');

        if ($request && $request->has('filter')) {
            if (array_key_exists('method', $request->filter)) {
                $session->where('method', $request->filter['method']);
            }

            if (array_key_exists('search', $request->filter)) {
                $search = $request->filter['search'];
                $type = $request->filter['search_type'];

                if (in_array($type, $search_in_user)) {
                    $session = $session->whereHas('user', function ($query) use ($search, $type) {
                        if ($type == 'account_name') {
                            $query = $query->where('first_name', 'LIKE', '%' . $search . '%');
                            $query = $query->orWhere('last_name', 'LIKE', '%' . $search . '%');
                            $query = $query->orWhere(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', '%' . $search . '%');
                        } else {
                            $query->where($type, 'LIKE', '%' . $search . '%');
                        }
                    });
                } else {
                    $session = $session->where($type, $search);
                }
            }

            if (array_key_exists('date_from', $request->filter) && array_key_exists('date_to', $request->filter)) {
                $session = $session->whereBetween('created_at', [
                    $request->filter['date_from'] . ' 00:00:00',
                    $request->filter['date_to'] . ' 23:59:59'
                ]);
            } else {
                if (array_key_exists('date_from', $request->filter)) {
                    $session = $session->where('created_at', '>=', $request->filter['date_from'] . ' 00:00:00');
                }

                if (array_key_exists('date_to', $request->filter)) {
                    $session = $session->where('created_at', '<=', $request->filter['date_to'] . ' 23:59:59');
                }
            }
        }

        if (isset($request['per_page'])) {
            return $session->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        return $session->get();
    }

    public function addUserSession(array $parameter): object
    {
        return $this->userSession->create($parameter);
    }
}
