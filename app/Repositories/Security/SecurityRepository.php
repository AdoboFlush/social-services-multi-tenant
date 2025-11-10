<?php

namespace App\Repositories\Security;

use App\Security;

class SecurityRepository implements SecurityInterface
{
    private $model;

    public function __construct(Security $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {

        return $this->model->updateOrCreate(['user_id' => $request['user_id']],$request);
    }

    public function update($id, $request, $timestamp = true)
    {
        $security = $this->model->where("user_id",$id)->first();
        if ($security) {
            $security->timestamps = $timestamp;
            $security->update($request);
            return $security;
        }
        return $this->model->updateOrCreate(['user_id' => $id],$request);
    }

    public function delete($id)
    {
        $security = $this->model->find($id);
        if ($security) {
            $security->delete();
            return $security;
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
