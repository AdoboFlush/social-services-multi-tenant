<?php

namespace App\Repositories\RoleHasPermission;

use App\RoleHasPermission;

class RoleHasPermissionRepository implements RoleHasPermissionInterface
{
    private $model;

    public function __construct(RoleHasPermission $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $role = $this->model->find($id);
        if ($role) {
            $role->update($request);
            return $role;
        }
        return false;
    }

    public function delete($id)
    {
        $role = $this->model->find($id);
        if ($role) {
            $role->delete();
            return $role;
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

    public function deleteByRole($role_id)
    {
        $permissions = $this->model->where('role_id', $role_id);
        if ($permissions) {
            $permissions->delete();
            return $permissions;
        }
        return false;
    }

    public function getByRole($role_id)
    {
        return $this->model->with('Permissions')->where('role_id', $role_id)->get();
    }
}
