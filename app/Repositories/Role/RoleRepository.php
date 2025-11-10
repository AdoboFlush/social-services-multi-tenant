<?php

namespace App\Repositories\Role;

use App\Role;

class RoleRepository implements RoleInterface
{
    private $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $deposit = $this->model->find($id);
        if ($deposit) {
            $deposit->update($request);
            return $deposit;
        }
        return false;
    }

    public function delete($id)
    {
        $deposit = $this->model->find($id);
        if ($deposit) {
            $deposit->delete();
            return $deposit;
        }
        return false;
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
}
