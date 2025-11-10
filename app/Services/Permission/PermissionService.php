<?php

namespace App\Services\Permission;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Requests\CreatePermissionRequest;

use App\Services\BaseService;
use App\Repositories\Permission\PermissionInterface;

use Auth;
use DB;

class PermissionService extends BaseService
{
	const LOGS_CREATING = 'CREATING PERMISSION:';
	const LOGS_FETCHING = 'FETCHING PERMISSION:';
	const LOGS_DELETING = 'DELETING PERMISSION:';

	const API_ERROR_UNEXPECTED = array('code' => 'E-PERMISSION-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

	protected $permissionInterface;

	public function __construct(PermissionInterface $permissionInterface) 
	{
        $this->permissionInterface = $permissionInterface;
    }

    public function create(Request $request)
    {
    	try {
    		Log::info(self::LOGS_CREATING);

            $validator = $this->validateCreatePermissionRequest($request);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $payload = [
                'name' => $request->name,
                'guard_name' => 'web'
            ];
    		return $this->permissionInterface->create($payload);
    	} catch(Exception $e) {
    		$message = $this->getErrorMessage($e);
            Log::error(self::LOGS_CREATING . ' - ' . $message);
            return view()->with('error', _lang($message));
    	}
    }   

    public function getAll($request)
    {
    	try {
            Log::info(self::LOGS_FETCHING);
            $permission = $this->permissionInterface->getAll();
            return $permission;
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_FETCHING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function delete($id)
    {
    	try {
            Log::info(self::LOGS_DELETING);
            $permission = $this->permissionInterface->delete($id);
            return $permission;
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_DELETING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    private function validateCreatePermissionRequest($request)
    {
        $createPermissionRequest = new CreatePermissionRequest();
        $rules = $createPermissionRequest->rules();
        return Validator::make($request->all(), $rules);
    }
}
