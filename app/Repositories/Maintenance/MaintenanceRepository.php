<?php

namespace App\Repositories\Maintenance;

use App\Maintenance;
use Illuminate\Database\Eloquent\Collection;

class MaintenanceRepository implements MaintenanceInterface
{
    private $model;

    public function __construct(Maintenance $model)
    {
        $this->model = $model;
    }

    public function create($request): Maintenance
    {
        return $this->model->create($request);
    }

    public function update($id, $request): bool
    {
        return $this->model->find($id)->update($request);
    }

    public function delete($id): bool
    {
        return $this->model->find($id)->delete();
    }

    public function get($id): Maintenance
    {
        return $this->model->find($id);
    }

    public function getAll(): Collection
    {
        return $this->model->with(['affiliate_codes'])->get();
    }
}