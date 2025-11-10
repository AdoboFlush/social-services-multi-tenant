<?php

namespace App\Repositories\AccountFee;

use App\AccountFee;

class AccountFeeRepository implements AccountFeeInterface
{
    private $model;

    public function __construct(AccountFee $model)
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
        $accountFee = $this->model->find($id);
        if ($accountFee) {
            $accountFee->update($request);
            return $accountFee;
        }
        return false;
    }

    public function delete($id)
    {
        $accountFee = $this->model->find($id);
        if ($accountFee) {
            $accountFee->delete();
            return $accountFee;
        }
        return false;
    }


    public function where($payload, $get = 'get')
    {
        return $this->model->where($payload)->$get();
    }
}
