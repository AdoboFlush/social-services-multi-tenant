<?php

namespace App\Repositories;

use App\Currency;
use App\User;
use App\Transaction;
use DB;

/** 
* Class UserRepository
* 
* @package App\Repositories
*/
class UserRepository 
{

    /**
    * @var $user
    */
    protected $user;

    /**
     * @var $transaction
     */
    protected $transaction;

    /**
     * @var $currency
     */
    protected $currency;


    /**
    * UserRepository constructor.
    *
    * @param User $user
    * @param Transaction $transaction
    * @param Account $account
    */
    public function __construct(User $user, Transaction $transaction, Currency $currency)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->currency = $currency;
    }
    
    /**
    * Get model by id.
    *
    * @param int $id
    *
    * @return \App\User
    */
    public function getById(int $id)
    {
        return $this->user->findOrFail($id);
    }

    public function update($id, $request)
    {
        $user = $this->model->find($id);
        if ($user) {
            $user->update($request);
            return $user;
        }
        return false;
    }

    /**
     * @param string $accountNumber
     *
     * @return boolean
     */
    public function checkAccountNumberIfExists(string $accountNumber)
    {
        return $this->user->where('account_number', $accountNumber)->exists();
    }

    /**
     * @param array $filters
     *
     * @return User
     */
    public function getAllUsers(array $filters)
    {
        $users = $this->user->with('user_information');

        if (!empty($filters)) {
            foreach ($filters as $key => $filter) {
                $users = $users->where($key, '=', $filter);
            }
        }
        return $users->orderBy('created_at', 'desc')->get();
    }

    public function getAll($request, $export = false) : Object
    { 
        DB::enableQueryLog();
        $user = $this->user->with('affiliate_details', 'user_information','accounts')->where(['user_type' => 'user'])->orderBy('created_at', 'desc');
        $user = $this->filter($user,$request, $export);
        return $user;
    }

    protected function filter($user, $request, $export = false) : Object
    {
        if ($request->has('filter')) {
            $filter_keys = ['date_from', 'date_to', 'search', 'code', 'account_status', 'account_type'];
            
            if(array_key_exists('account_number', $request->filter)){

                $search = $request->filter['account_number'];
                $user = $user->where('account_number', $search);

            }else{

                foreach ($request->filter as $key => $filter) {
                    if (!in_array($key, $filter_keys)) {
                        $user = $user->where($key, 'LIKE', '%' . $filter . '%');
                    }
                }

            }


            if (array_key_exists('account_status', $request->filter)) {
                $status = $request->filter['account_status'];
                if ($status == 'Dormant') {
                    $user = $user->where('is_dormant', 1);
                } else {
                    $user = $user->where('account_status', $request->filter['account_status'])
                        ->where('is_dormant', 0);
                }
            }

            if (array_key_exists('account_type', $request->filter)) {
                $user = $user->where('account_type', $request->filter['account_type'] );
            }

            if (array_key_exists('code', $request->filter) ) {
                $code = $request->filter['code'];
                $user = $user->whereHas('affiliate_details', function ($query)  use ($code)  {
                    return $query->where('parent_code', $code);
                });
            }            

            if (array_key_exists('date_from', $request->filter) && array_key_exists('date_to', $request->filter)) {
                $user = $user->whereBetween('created_at', [
                    $request->filter['date_from'] . ' 00:00:00',
                    $request->filter['date_to'] . ' 23:59:59'
                ]);
            } else {
                if (array_key_exists('date_from', $request->filter)) {
                    $user = $user->where('created_at', '>=', $request->filter['date_from'] . ' 00:00:00');
                }

                if (array_key_exists('date_to', $request->filter)) {
                    $user = $user->where('created_at', '<=', $request->filter['date_to'] . ' 23:59:59');
                }
            }
            

            if (array_key_exists('search', $request->filter)) {
                $search = $request->filter['search'];
                $user = $user->where(function ($query)  use ($request, $search)  {

                    if (!array_key_exists('account_type', $request->filter)) {
                        $query->where('account_type', 'LIKE', '%' . $search . '%' );
                    }

                    if (!array_key_exists('account_number', $request->filter)) {                    
                        $query->orWhere('account_number', 'LIKE', '%' . $search . '%');
                    }

                    if (!array_key_exists('first_name', $request->filter)) {                    
                        $query->orWhere('first_name', 'LIKE', '%' . $search . '%');
                    }

                    if (!array_key_exists('last_name', $request->filter)) {                    
                        $query->orWhere('last_name', 'LIKE', '%' . $search . '%');
                    }

                    if (!array_key_exists('email', $request->filter)) {                        
                        $query->orWhere('email', 'LIKE', '%' . $search . '%');
                    }

                    if (!array_key_exists('account_status', $request->filter)) {                    
                        $query->orWhere('status', 'LIKE', '%' . $search . '%');
                    }

                   
                });   
            }
        }
        

        if (isset($request['per_page'])) {
           $user = $user->paginate($request['per_page'], ['*'], 'page', $request['page']);
           return $user;
        }
        
        if($export){
            return $user;
        }

        return $user->get();

    }

    /**
     * @param string $transactionType
     * @param string $currency
     *
     * @return Transaction
     */
    public function getUserTransactionsByTypeAndCurrency(array $options)
    {
        if ($options['type'] == 'withdrawal') {
            return $this->transaction->select('wire_transfer_details.debit_amount')
                ->join('users','transactions.user_id','users.id')
                ->join('accounts','transactions.account_id','accounts.id')
                ->join('wire_transfer_details','transactions.transaction_number','wire_transfer_details.transaction_number')
                ->where('accounts.currency', $options['currency'])
                ->where('type',$options['type'])
                ->where('users.access_type' ,$options['access_type'])
                ->where('transactions.status', 'completed')
                ->whereYear('transactions.created_at', '=', $options['year'])
                ->sum('wire_transfer_details.debit_amount');
        } else {
            return $this->transaction->select('amount')
                ->join('users','transactions.user_id','users.id')
                ->join('accounts','transactions.account_id','accounts.id')
                ->where('accounts.currency', $options['currency'])
                ->where('type',$options['type'])
                ->where('users.access_type' ,$options['access_type'])
                ->where('transactions.status', 'completed')
                ->whereYear('transactions.created_at', '=', $options['year'])
                ->sum('amount');
        }
    }

    /**
     * @param int $userId
     * @param int $limit
     *
     * @return Transaction
     */
    public function getUserTransactionsByUserId(int $userId, int $limit = 10)
    {
        $transactions = $this->transaction
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('dr_cr', 'asc')
            ->limit($limit)
            ->get();

        return !empty($transactions) ? $transactions : [];
    }

    /**
     * @param int $userId
     *
     * @return Account
     */
    public function getUserCurrencyAccountsByUserId(int $userId)
    {
        $accounts =  $this->currency->select(
            'currency.name',
            'accounts.status',
            'accounts.opening_balance',
            'accounts.id')
            ->leftJoin('accounts', function($join) use ($userId) {
                $join->on('currency.name', '=', 'accounts.currency')
                    ->where('accounts.status', 1)
                    ->where('accounts.user_id', $userId);
            })
            ->where('currency.status', 1)
            ->get();
        return !empty($accounts) ? $accounts : [];
    }

    /**
     *  @param  $year
     *  @param  $currency
     *  @param  $type
     *
     * @return Transaction
     */
    public function getUserTotalTransactionPerMonthByYearAndCurrency($year, $currency, $type)
    {
        return $this->transaction->select(
           'm.month',DB::raw('IFNULL(SUM(transactions.amount),0) as amount')
       )
            ->from(
                DB::raw(
                    '(SELECT 1 AS MONTH UNION SELECT 2 AS MONTH UNION SELECT 3 AS MONTH UNION 
                    SELECT 4 AS MONTH UNION SELECT 5 AS MONTH UNION SELECT 6 AS MONTH UNION 
                    SELECT 7 AS MONTH UNION SELECT 8 AS MONTH UNION SELECT 9 AS MONTH UNION 
                    SELECT 10 AS MONTH UNION SELECT 11 AS MONTH UNION SELECT 12 AS MONTH ) AS m'
                )
            )
            ->join('transactions', function($join) use ($year, $currency, $type) {
                $join->on('m.month', '=', DB::raw('MONTH(transactions.created_at)'))
                    ->whereYear('transactions.created_at', $year)
                    ->where('transactions.type',$type)
                    ->where('currency',$currency);
            })
            ->join('users','transactions.user_id','users.id')
            ->where('users.access_type' , 'live')
            ->orderBy('m.month','asc')
            ->groupBy('m.month')
            ->get();
    }

    public function getUserByAccountNumber($account_number)
    {
        return $this->user->where('account_number', $account_number)->first();
    }

}