<?php

namespace App\Repositories\PasswordResets;

use App\PasswordResets;

class PasswordResetsRepository implements PasswordResetsInterface
{
    private $model;

    public function __construct(PasswordResets $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email',$email)->first();
    }

    public function update($id, $request)
    {
        $passwordReset = $this->model->find($id);
        if ($passwordReset) {
            $passwordReset->update($request);
            return $passwordReset;
        }
        return false;
    }

    public function delete($id)
    {
        $passwordReset = $this->model->find($id);
        if ($passwordReset) {
            $passwordReset->delete();
            return $passwordReset;
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
