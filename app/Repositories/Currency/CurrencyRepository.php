<?php

namespace App\Repositories\Currency;

use App\Currency;

class CurrencyRepository implements CurrencyInterface
{
    private $model;

    public function __construct(Currency $model)
    {
        $this->model = $model;
    }

    public function create($name)
    {
        return $this->model->firstOrCreate(
            ['name' => $name],
            ['status' => 1 ]
        );
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getAll()
    {
        return $this->model->get();
    }

    public function getAllActive($pluck = false)
    {
        if ($pluck) {
            return $this->model->where('status', 1)->pluck('name');    
        }
        return $this->model->where('status', 1)->get();
    }

    public function update($id, $request)
    {
        $currency = $this->model->find($id);
        if ($currency) {
            $currency->update($request);
            return $currency;
        }
        return false;
    }

    public function delete($id)
    {
        $currency = $this->model->find($id);
        if ($currency) {
            $currency->delete();
            return $currency;
        }
        return false;
    }


    public function where($payload, $get = 'get')
    {
        return $this->model->where($payload)->$get();
    }

    public function getCurrenciesAndAccounts($user_id)
    {
        $accounts = $this->model->select(
            'currency.name',
            'accounts.status',
            'accounts.opening_balance',
            'accounts.id'
        )
            ->leftJoin('accounts', function($join) use ($user_id) {
                $join->on('currency.name', '=', 'accounts.currency')
                    ->where('accounts.status', 1)
                    ->where('accounts.user_id', $user_id);
            })
            ->where('currency.status', 1)
            ->get();
        return $accounts;
    }
}
