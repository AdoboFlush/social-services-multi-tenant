<?php

namespace App\Repositories\RoleHasPermission;

use App\Repositories\BaseInterface;

interface RoleHasPermissionInterface extends BaseInterface
{
	public function deleteByRole($role_id);
	public function getByRole($role_id);
}
