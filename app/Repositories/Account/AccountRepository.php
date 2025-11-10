<?php

namespace App\Repositories\Account;

use App\Account;

class AccountRepository implements AccountInterface
{
    private $model;

    public function __construct(Account $model)
    {
        $this->model = $model;
    }

    public function create($params)
    {
        return $this->model->create($params);
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getAll()
    {
        $model = $this->model;
        return $model->get();
    }

    public function update($id, $request)
    {
        $account = $this->model->find($id);
        if ($account) {
            $account->update($request);
            return $account;
        }
        return false;
    }

    public function delete($id)
    {
        $account = $this->model->find($id);
        if ($account) {
            $account->delete();
            return $account;
        }
        return false;
    }

    public function updateAmountByUserIdAndCurrency($id,$currency,$amount)
    {
		$account = $this->model->firstOrCreate(array(
            'user_id' => $id,
            'currency' => $currency
        ));
        $account->opening_balance += $amount;
        $account->status = 1;
        $account->save();
        return $account;
    }

    public function getAccountsByUserId($user_id, $select = [])
    {
        if (!empty($select)) {
            return $this->model->select($select)->where('user_id', $user_id)->get();
        }
        return $this->model->where('user_id', $user_id)->get();
    }

    public function getAccountsByUserIdOrderByPriority(int $user_id) : object
    {
        return $this->model->where("user_id", $user_id)->with("currency_status")->get()->sort(function($current, $next){
            if(!is_null($current->currency_status) && !is_null($next->currency_status)){
                return $current->currency_status->priority - $next->currency_status->priority;
            }
        });
    }

    public function where($payload, $get = 'get')
    {
        return $this->model->where($payload)->$get();
    }

    public function firstOrCreate($request)
    {
        return $this->model->firstOrCreate($request);
    }
}
