<?php

namespace App\Repositories\Transaction;

use App\Transaction;
use App\WireTransfer;
use App\Account;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Auth;
use DB;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Log;

class TransactionRepository implements TransactionInterface
{
    private $model;

    public function __construct(Transaction $model)
    {
        $this->model = $model;
    }

    public function create(array $request, bool $timestamp = true): Transaction
    {
        $model = $this->model;
        if (!$timestamp) {
            $model->timestamps = false;
            foreach ($request as $key => $value) {
                $model->{$key} = $value;
            }
            $model->save();
            return $model;
        }
        return $model->create($request);
    }

    public function update(int $id, array $request): Transaction
    {
        $deposit = $this->model->find($id);
        if ($deposit) {
            $deposit->update($request);
        }
        return $deposit;
    }

    public function delete(int $id): Transaction
    {
        $deposit = $this->model->find($id);
        if ($deposit) {
            $deposit->delete();
        }
        return $deposit;
    }

    public function get(int $id): Transaction
    {
        return $this->model->find($id);
    }

    public function getAll(): Transaction
    {
        return $this->model->get();
    }

    public function getByPaymentRequestRefId(int $ref_id): Transaction
    {
        return $this->model
            ->where('ref_id', $ref_id)
            ->where('type', 'payment_request')->first();
    }

    public function getByReferenceId(int $ref_id): Transaction
    {
        return $this->model->where("ref_id", $ref_id)->first();
    }

    public function getByTransactionNumber(string $transaction_number): Transaction
    {
        return $this->model->where("transaction_number", $transaction_number)->first();
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator | Transaction
     */
    public function getWithdrawalRequestsByUser(int $id, array $request = null)
    {
        $where = array(
            'type' => $this->model::TYPE_BULK_WITHDRAW,
            'user_id' => $id,
        );
        $model = $this->model->where($where)->orderBy('created_at', 'desc');

        if (!is_null($request)) {
            $per_page = isset($request['per_page']) ? $request['per_page'] : 10;
            $page = isset($request['page']) ? $request['page'] : 1;
            $model = $model->paginate($per_page, ['*'], 'page', $page);
            return $model;
        } else {
            return $model->get();
        }
    }

    public function getWithdrawalRequestsBy(string $status): Collection
    {
        $order = $status == "applying" ? "asc" : "desc";
        $where = array(
            'type' => $this->model::TYPE_BULK_WITHDRAW,
            'status' => "$status",
        );
        return $this->model->where($where)->orderBy('created_at', $order)->get();
    }

    public function getByTypeAndUserId(string $type, int $id, array $customWhere = []) : Collection
    {
        $where = array(
            'type' => $type,
            'user_id' => $id,
        );
        $model = $this->model->where($where);
        if(count($customWhere) > 1){
            $customWhereClause = isset($customWhere[0]) ? $customWhere[0] : '';
            $customWhereValue = isset($customWhere[1])  ? $customWhere[1] : [];
            if(!empty($customWhereClause) && !empty($customWhereValue)){
                $model = $model->whereRaw($customWhereClause, $customWhereValue);
            }
        }
        $model = $model->orderBy('created_at', 'desc');
        return $model->get();
    }

    public function updateByReferenceId(int $id, Request $request): Transaction
    {
        $transaction = $this->model->where('ref_id', $id);
        $transaction = $transaction->where(function ($query) {
            $query->where('type', $this->model::TYPE_WITHDRAWAL)
                ->orWhere('type', $this->model::TYPE_BULK_WITHDRAW);
        })->first();
        $transaction->update($request->all());
        return $transaction;
    }

    public function deleteByTransactionNumber(string $transaction_number): Transaction
    {
        $transaction = $this->model->where('transaction_number', $transaction_number)->first();
        if ($transaction) {
            if ($transaction->type == $this->model::WITHDRAWAL) {
                $transaction->account->opening_balance = $transaction->account->opening_balance
                    + $transaction->wire_transfer->debit_amount
                    + $transaction->fee;
            } elseif ($transaction->type == $this->model::TYPE_BULK_WITHDRAW) {
                $transaction->account->opening_balance = $transaction->account->opening_balance
                    + $transaction->amount
                    + $transaction->fee;
            }

            foreach ($transaction->bulkWithdrawals as $withdrawal) {
                $withdrawal->delete();
            }
            
            $transaction->account->save();
            $transaction->delete();
        }
        return $transaction;
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator | Collection
     */
    public function getAllUserTransaction(Request $request, bool $doExport = false)
    {
        $date1 = $request->has('filter') && isset($request->filter['date1']) ? $request->filter['date1'] : null;
        $date2 = $request->has('filter') && isset($request->filter['date2']) ? $request->filter['date2'] : null;
        $type = $request->has('filter') && isset($request->filter['type']) ? $request->filter['type'] : "all";
        $status = $request->has('filter') && isset($request->filter['status']) ? $request->filter['status'] : "all";
        $currency = $request->has('filter') && isset($request->filter['currency'])
            ? $request->filter['currency']
            : "all";

        $transactions = $this->model->select('*', DB::raw('COALESCE(approval_date, created_at) AS app_date'))
            ->where('user_id', $request->has('user_id') ? $request->user_id : Auth::id());

        if ($request->has('filter') && array_key_exists('search', $request->filter) && array_key_exists('search_type', $request->filter)) {
            $search_in_user = ['account_number', 'account_name'];
            $search = $request->filter['search'];
            $search_type = $request['filter']['search_type'];

            if(in_array($search_type, $search_in_user)){

                $transactions = $transactions->where(function ($query) use ($search_type, $search){
                    $query->whereHas('parent', function ($query) use ($search_type, $search){
                        $query->whereHas('user', function ($query) use ($search_type, $search){
                            if ($search_type === 'account_name'){
                                $query->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', '%' . $search . '%');
                            } else {
                                $query->where($search_type, 'LIKE', '%' . $search . '%');
                            }
                        });
                    })->orWhereHas('child', function ($query) use ($search_type, $search){
                        $query->whereHas('user', function ($query) use ($search_type, $search){
                            if ($search_type === 'account_name'){
                                $query->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', '%' . $search . '%');
                            } else {
                                $query->where($search_type, 'LIKE', '%' . $search . '%');
                            }
                        });
                    });
                });

            } else {
                $transactions = $transactions->where($search_type, $search);
            }
        }

        $transactions->where(function ($query) use ($type) {
            if ($type == 'all') {
                $query->where('status', $this->model::STATUS_COMPLETED);
                $query->orWhere('status', $this->model::STATUS_APPLYING)->where('type', 'withdrawal');
                $query->orWhere('status', $this->model::STATUS_CANCELED)->where('type', 'withdrawal');
                $query->orWhere('status', $this->model::STATUS_CANCELED)->where('type', Transaction::TYPE_CARD_TOPUP);
            } else {
                $query->where('type', $type);
            }
        });

        if ($status != 'all') {
            $transactions = $transactions->where('status', $status);
        }

        if ($currency != 'all') {
            $transactions->whereHas("account", function ($query) use ($currency) {
                $query->where('currency', $currency);
            });
        }

        if (!is_null($date1) && !is_null($date2)) {
            $transactions = $transactions->whereBetween('created_at', [
                $date1 . ' 00:00:00',
                $date2 . ' 23:59:59'
            ]);
        } else {
            if (!is_null($date1)) {
                $transactions = $transactions->where('created_at', '>=', $date1 . ' 00:00:00');
            }

            if (!is_null($date2)) {
                $transactions = $transactions->where('created_at', '<=', $date2 . ' 23:59:59');
            }
        }

        $transactions = $transactions->orderBy('balance_order_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if($doExport){
            return $transactions;
        }
        
        if(!is_null($request->pagination)){
            $per_page = isset($request['per_page']) ? $request['per_page'] : 10;
            $page = isset($request['page']) ? $request['page'] : 1;
            return $transactions->paginate(
                $per_page,
                ['*'],
                http_build_query($request->except('page')) . "&page",
                $page
            );
        }
        return $transactions->get();
    }

    public function getTransactions(Transaction $transaction)
    {
        return $this->model->select('*', DB::raw('COALESCE(approval_date, created_at) AS app_date'))
            ->where('account_id', $transaction->account_id)
            ->where('user_id', $transaction->user_id)
            ->orderBy('app_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function updateSucceedingTransactions(Transaction $transaction)
    {
        $_transactions = $this->getTransactions($transaction);
        foreach ($_transactions as $_transaction) {
            if ($transaction->transaction_number == $_transaction->transaction_number) {
                break;
            }

            if ($transaction->type == $this->model::TYPE_WITHDRAWAL) {
                $_transaction->current_balance = floatval($_transaction->current_balance)
                    + floatval($transaction->wire_transfer->debit_amount)
                    + floatval($transaction->fee);
            } elseif ($transaction->type == $this->model::TYPE_BULK_WITHDRAW) {
                $_transaction->current_balance = floatval($_transaction->current_balance)
                    + floatval($transaction->amount)
                    + floatval($transaction->fee);
            } elseif ($transaction->type == $this->model::TYPE_DEPOSIT) {
                $_transaction->current_balance = floatval($_transaction->current_balance)
                    - floatval($transaction->amount);
            }
            $_transaction->save();
        }
    }

    /**
     * @return Collection |  Paginator
     */
    public function getAllTransactions(Request $request, bool $doExport = false)
    {
        $transactions = $this->model->with('internalTransfer', 'parent', 'account', 'wire_transfer', 'deposit')
            ->select('transactions.*',
                DB::raw('COALESCE(transactions.approval_date, transactions.created_at) AS app_date')
            )
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->leftJoin('transactions as parent', 'parent.id', '=', 'transactions.parent_id')
            ->where(function ($query) {
                $query->orWhereIn('transactions.type', [
                    $this->model::TYPE_INTERNAL_TRANSFER,
                    $this->model::TYPE_EXCHANGE_RATE,
                    $this->model::TYPE_PAYMENT_REQUEST
                ])
                    ->where(function ($q) {
                        $q->whereNotNull('transactions.parent_id')->where("transactions.status", $this->model::STATUS_COMPLETED);
                    })
                ->orWhereIn('transactions.type', [$this->model::TYPE_DEPOSIT, $this->model::TYPE_REFUND])
                    ->where(function ($q) {
                        $q->where("transactions.status", $this->model::STATUS_COMPLETED)->where('transactions.parent_id', null);
                    })
                ->orWhereIn('transactions.type', [
                    $this->model::TYPE_CARD_TOPUP,
                    $this->model::TYPE_INACTIVITY_FEE,
                    $this->model::TYPE_DORMANCY_FEE,
                    $this->model::TYPE_MONTHLY_FEE
                ])
                    ->where(function ($q) {
                        $q->whereIn("transactions.status", [
                            $this->model::STATUS_APPLYING,
                            $this->model::STATUS_CANCELED,
                            $this->model::STATUS_COMPLETED
                        ])
                        ->where('transactions.parent_id', null);
                    })
                ->orWhereIn('transactions.type', [
                    $this->model::TYPE_WITHDRAWAL,
                    $this->model::TYPE_WIRE_TRANSFER,
                    $this->model::TYPE_BULK_WITHDRAW,
                ])
                    ->where(function (Builder $q) {
                        $q->whereHas('wire_transfer', function (Builder $qry){
                            $qry->where(['method' => WireTransfer::METHOD_WITHDRAWAL_JP])
                                ->where(function(Builder $wire){
                                    $wire->whereHas('user', function(Builder $user) {
                                        $user->where('account_type', Account::BUSINESS);
                                    })->where('status', $this->model::STATUS_COMPLETED);

                                    $wire->orWhereHas('user', function(Builder $user) {
                                        $user->where('account_type', Account::PERSONAL);
                                    })->whereIn('status', [
                                        $this->model::STATUS_APPLYING,
                                        $this->model::STATUS_CANCELED,
                                        $this->model::STATUS_COMPLETED
                                    ]);
                                })
                            ->orWhere('method', '!=', WireTransfer::METHOD_WITHDRAWAL_JP)
                                ->where(function(Builder $wire){
                                    $wire->whereIn('status', [
                                        $this->model::STATUS_APPLYING,
                                        $this->model::STATUS_CANCELED,
                                        $this->model::STATUS_COMPLETED
                                    ]);
                                });
                        })
                        ->where('transactions.parent_id', null);
                    });
            })
            /*end*/
            ->orderBy('app_date', 'desc')
            ->orderBy('transactions.id', 'desc');


        if ($request->has('filter')) {
            $transactions = $this->filter($request, $transactions);
        }

        if($doExport){
            return $transactions;
        }

        if (isset($request['per_page'])) {
            return $transactions->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        return $transactions->get();
    }

    private function filter(Request $request, Builder $model)
    {
        foreach ($request->filter as $key => $filter) {
            if ($key != 'date_from' && $key != 'date_to' && $key != 'search') {
                if ($key == 'user_id') {
                    $model = $model->where(function ($query) use ($filter) {
                        $query->where("transactions.user_id", $filter)->orWhere("parent.user_id", $filter);
                    });
                } elseif ($key === 'type') {
                    if ($filter == 'withdrawal') {
                        $model = $model->whereIn("transactions.type",  [
                            $this->model::TYPE_WITHDRAWAL,
                            $this->model::TYPE_BULK_WITHDRAW,
                            $this->model::TYPE_WIRE_TRANSFER
                        ]);
                    } else {
                        $model = $model->where(["transactions.{$key}" => $filter]);
                    }
                } else {
                    $model = $model->where(["transactions.{$key}" => $filter]);
                }
            }
        };

        if (array_key_exists('search', $request->filter)) {
            $search = $request->filter['search'];
            $model = $model->where(function ($query) use ($request, $search) {
                if (!array_key_exists('user_id', $request->filter)) {
                    $query = $query->where(function($q) use ($search){
                        $q->where('users.first_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('users.last_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('users.email', 'LIKE', '%' . $search . '%')
                            ->orWhere('users.account_number', 'LIKE', '%' . $search . '%');
                    });
                }

                if (!array_key_exists('transaction_number', $request->filter)) {
                    $query = $query->orWhere('transactions.transaction_number', 'LIKE', '%' . $search . '%');
                }

                if (!array_key_exists('currency', $request->filter)) {
                    $query = $query->orWhere('transactions.currency', 'LIKE', '%' . $search . '%');
                }

                if (!array_key_exists('type', $request->filter)) {
                    $query = $query->orWhere('transactions.type', 'LIKE', '%' . $search . '%');
                }

                if (!array_key_exists('status', $request->filter)) {
                    $query = $query->orWhere('transactions.status', 'LIKE', '%' . $search . '%');
                }
            });
        }

        if (array_key_exists('date_from', $request->filter)) {
            $filterDateFrom = Carbon::parse($request->filter['date_from'])->startOfDay();
        }

        if (array_key_exists('date_to', $request->filter)) {
            $filterDateTo = Carbon::parse($request->filter['date_to'])->endOfDay();
        }


        if (isset($filterDateFrom) && isset($filterDateTo)) {
            $model = $model->whereBetween('transactions.updated_at', [
                $filterDateFrom,
                $filterDateTo
            ]);
        } else {
            if (isset($filterDateFrom)) {
                $model = $model->where(
                    'transactions.updated_at',
                    '>=',
                    $filterDateFrom
                );
            }

            if (isset($filterDateTo)) {
                $model = $model->where('transactions.updated_at', '<=', $filterDateTo);
            }
        }
        return $model;
    }

    public function updateByRefType(int $id, string $type, array $param): bool
    {
        return $this->model->where('ref_id', $id)->where('type', $type)->update($param);
    }
    
    public function getLastTransactionByBalanceOrder(Transaction $transaction): ?Transaction
    {
        return Transaction::where('user_id', $transaction->user_id)
            ->where('account_id', $transaction->account_id)
            ->whereNotNull('balance_order_id')
            ->orderBy('balance_order_id', 'desc')
            ->first();
    }
    
    public function getLatestBalanceOrder(Transaction $transaction): ?int
    {
        return Transaction::where('user_id', $transaction->user_id)
            ->orderBy('balance_order_id', 'desc')
            ->first()
            ->balance_order_id;
    }
    
    public function getLastTransactionWithBalance(Transaction $transaction): ?Transaction
    {
        return Transaction::where('user_id', $transaction->user_id)
            ->where('account_id', $transaction->account_id)
            ->where('id', '<', $transaction->id)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function getTransactionsByAccount(int $account_id, SupportCollection $filter): Builder
    {
        $currency = $filter->has('currency') ? $filter['currency'] : 'all';

        $transactions = Transaction::with(['wire_transfer', 'account'])->where('account_id', $account_id)
            ->where(function ($query) {
                $query->where('status', Transaction::STATUS_COMPLETED);
                $query->orWhere('status', Transaction::STATUS_APPLYING)->where('type', Transaction::TYPE_WITHDRAWAL);
                $query->orWhere('status', Transaction::STATUS_CANCELED)->where('type', Transaction::TYPE_WITHDRAWAL);
                $query->orWhere('status', Transaction::STATUS_CANCELED)->where('type', Transaction::TYPE_CARD_TOPUP);
            });

        if ($filter->has('from') && $filter->has('to')) {
            $transactions = $transactions->whereBetween('created_at', [
                Carbon::parse($filter['from'])->startOfDay(),
                Carbon::parse($filter['to'])->endOfDay(),
            ]);
        } else {
            if ($filter->has('from')) {
                $transactions = $transactions->where('created_at', '>=', Carbon::parse($filter['from'])->startOfDay());
            }

            if ($filter->has('to')) {
                $transactions = $transactions->where('created_at', '<=', Carbon::parse($filter['to'])->endOfDay());
            }
        }

        if($currency !== 'all'){
            $transactions->whereHas('account', function ($query) use ($currency) {
                $query->where('currency', $currency);
            });
        }

        return $transactions;
    }

}
