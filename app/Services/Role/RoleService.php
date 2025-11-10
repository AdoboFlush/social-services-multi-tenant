<?php

namespace App\Services\Role;

use Exception;
use Validator;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Http\Requests\CreateRoleRequest;

use App\Services\BaseService;
use App\Repositories\Role\RoleInterface;
use App\Repositories\RoleHasPermission\RoleHasPermissionInterface;
use App\Repositories\Permission\PermissionInterface;

use DB;
use Artisan;
use Auth;

class RoleService extends BaseService
{
	const LOGS_FETCHING = 'FETCHING ROLE:';
    const LOGS_CREATING = 'CREATING ROLE:';
    const LOGS_UPDATING = 'UPDATING ROLE:';

	const API_ERROR_UNEXPECTED = array('code' => 'E-ROLE-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

	protected $permissionInterface;
	protected $roleInterface;
    protected $roleHasPermissionInterface;

	public function __construct(
		PermissionInterface $permissionInterface,
		RoleInterface $roleInterface,
        RoleHasPermissionInterface $roleHasPermissionInterface
	) 
	{
        $this->permissionInterface = $permissionInterface;
        $this->roleHasPermissionInterface = $roleHasPermissionInterface;
        $this->roleInterface = $roleInterface;
    }

    public function create(Request $request)
    {
        try {
            Log::info(self::LOGS_CREATING);

            $validator = $this->validateCreateRoleRequest($request);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $param['name'] = $request->name;
            $param['guard_name'] = 'web';
            $role = $this->roleInterface->create($param);

            foreach ($request->permission as $permission) {
                $payload['permission_id'] = $permission;
                $payload['role_id'] = $role->id;

                $this->roleHasPermissionInterface->create($payload);
            }

            Artisan::call("permission:cache-reset");
            
            return $role;
        } catch(Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_CREATING . ' - ' . $message);
            return view()->with('error', _lang($message));
        }
    }  

    public function update(Request $request, $id)
    {
        try {
            Log::info(self::LOGS_UPDATING);

            $validator = $this->validateCreateRoleRequest($request);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            $param['name'] = $request->name;
            $role = $this->roleInterface->update($id, $param);

            $old = [];
            foreach($role->permissions as $permission){
                array_push($old,$permission->name);
            }
            $this->roleHasPermissionInterface->deleteByRole($id);

            foreach ($request->permission as $permission) {
                $payload['permission_id'] = $permission;
                $payload['role_id'] = $id;
                $this->roleHasPermissionInterface->create($payload);
            }
            DB::commit();

            Artisan::call("permission:cache-reset");

            $newRole = $this->roleInterface->get($id);
            $new = [];
            foreach($newRole->permissions as $permission){
                array_push($new,$permission->name);
            }
            $log = array('old'=>['permissions' => implode(",",$old)],'attributes'=>['permissions' => implode(",",$new)]);
            activity("Roles and Permission")
                ->causedBy(Auth::user())
                ->withProperties($log)
                ->log('updated '.$request->name);

            return $role;
        } catch(Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_CREATING . ' - ' . $message);
            return view()->with('error', _lang($message));
        }
    }   

    public function get($id)
    {
        try {
            Log::info(self::LOGS_FETCHING . ' ' . $id);
            $role = $this->roleInterface->get($id);
            $permissions = $this->roleHasPermissionInterface->getByRole($id);
            $temp = [];
            foreach ($permissions as $k => $v) {
                array_push($temp, $v->Permissions);
            }
            $role->permissions = $temp;
            return $role;
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_FETCHING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }


    public function getAll($request)
    {
    	try {
            Log::info(self::LOGS_FETCHING);
            $roles = $this->roleInterface->getAll($request);
            foreach ($roles as $key => $value) {
                
                $permissions = $this->roleHasPermissionInterface->getByRole($value->id);

                $temp = [];
                foreach ($permissions as $k => $v) {
                    array_push($temp, $v->Permissions);
                }
                $roles[$key]['permissions'] = $temp;    
            }
            return $roles;
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_FETCHING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    private function validateCreateRoleRequest($request)
    {
        $createRoleRequest = new CreateRoleRequest();
        $rules = $createRoleRequest->rules();
        return Validator::make($request->all(), $rules);
    }
}


