<?php

namespace App\Repositories\Country;

use App\Country;
use Illuminate\Support\Collection;

class CountryRepository implements CountryInterface
{
    private $model;

    public function __construct(Country $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->get();
    }

    public function getAllActive()
    {
        return $this->model->where("status","active")->get();
    }

    public function update(Int $id, Array $param)
    {
        $country = $this->model->find($id);
        if ($country) {
            $result = $country->update($param);
            if ($result) {
                return $result;
            }
        }
        abort(500, "Unable to find country with id: ".$id);
    }
}
