<?php

namespace App\Repositories\Setting;

use App\Setting;

class SettingRepository implements SettingInterface
{
    private $model;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function getValueByKey($key)
    {
        return $this->model->where('name',$key)->value('value');
    }

    public function getByKey($key)
    {
        return $this->model->where('name',$key)->first();
    }

    public function update($id, $request)
    {
        $setting = $this->model->find($id);
        if ($setting) {
            $setting->update($request);
            return $setting;
        }
        return false;
    }

    public function delete($id)
    {
        $setting = $this->model->find($id);
        if ($setting) {
            $setting->delete();
            return $setting;
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
