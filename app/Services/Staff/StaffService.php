<?php

namespace App\Services\Staff;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Services\BaseService;
use App\Repositories\Role\RoleInterface;
use App\Repositories\User\UserInterface;

use App\Http\Requests\CreateStaffRequest;
use App\Http\Requests\UpdateStaffRequest;

use Illuminate\Support\Facades\Mail;

use Auth;
use DB;
use Hash;

class StaffService extends BaseService
{

    protected $roleInterface;
    protected $userInterface;

    public function __construct(
    	RoleInterface $roleInterface,
        UserInterface $userInterface
    ) {
        $this->roleInterface = $roleInterface;
        $this->userInterface = $userInterface;
    }

    public function create($request)
    { 
        $roles = $this->roleInterface->getAll();
        if( ! $request->ajax()){            
           return view('backend.staff.create', compact('roles'));
        }else{
           return view('backend.staff.modal.create', compact('roles'));
        }
    }

    public function store($request) {
        $validator = $this->validateCreateStaffRequest($request);
        
        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return redirect('admin/staffs/create')
                            ->withErrors($validator)
                            ->withInput();
            }           
        }

        $affiliations_arr = $excluded_affiliations_arr = $brgy_access_arr = $hide_fields_arr = [];
        if($request->has('affiliation_access') && !empty($request->affiliation_access)) {
            $affiliations_arr = explode(";", $request->affiliation_access);
        }

        if($request->has('excluded_affiliations') && !empty($request->excluded_affiliations)) {
            $excluded_affiliations_arr = explode(";", $request->excluded_affiliations);
        }

        if($request->has('brgy_access') && !empty($request->brgy_access)) {
            $brgy_access_arr = explode(";", $request->brgy_access);
        }

        if($request->has('hide_fields') && !empty($request->hide_fields)) {
            $hide_fields_arr = explode(";", $request->hide_fields);
        }
        
        $restriction_properties = [
            "area" => $request->area_access, 
            "affiliations" => $affiliations_arr, 
            "excluded_affiliations" => $excluded_affiliations_arr,
            "brgy_access" => $brgy_access_arr,
            "has_export" => $request->has('has_export'),
            "bypass_update" => $request->has('bypass_update'),
            "has_activity_logs_access" => $request->has('has_activity_logs_access'),         
            "has_area_search" => $request->has('has_area_search'),
            "for_viewing_only" => $request->has('for_viewing_only'),
            "can_clear_field" => $request->has('can_clear_field'),   
            "hide_fields" => $hide_fields_arr
        ];

        $request->request->add([
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'user_access' => $request->user_access,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'restriction_properties' => json_encode($restriction_properties),
        ]);

        if ($request->hasFile('profile_picture')){
           $image = $request->file('profile_picture');
           $file_name = "profile_".time().'.'.$image->getClientOriginalExtension();
           $image->move(base_path('uploads/profile/'),$file_name);
           $profile_picture = $file_name;

           $request->request->add([
            'profile_picture' => $file_name
           ]);
        }
        $user = $this->userInterface->create($request->all());
        $user->assignRole($request->user_access);
        
        //Prefix Output
        $user->name = $user->first_name.' '.$user->last_name;
        $user->user_type = ucwords($user->user_type);
        
        $user->status = $user->status == 1 ? status(_lang('Active'),'success') : status(_lang('In-Active'),'danger');

        activity('Manager and Admin')
            ->performedOn($user)
            ->withProperties(array(
                "name" => $user->first_name . " " . $user->last_name,
                "email" => $user->email,
                "user type" => $user->user_type
            ))->log('Register');

        if(! $request->ajax()){
           return redirect('admin/staffs/create')->with('success', _lang('Saved Successfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Successfully'),'data'=>$user]);
        }
    }

    public function update($id, $request) 
    {

        $validator = $this->validateUpdateStaffRequest($id, $request);
        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return redirect()->route('staffs.edit', $id)
                            ->withErrors($validator)
                            ->withInput();
            }           
        }

        if(isset($request->password) && $request->password){
            $request->request->add([
                'password' => Hash::make($request->password),
            ]);
        } else {
            $request->request->remove('password');
        }

        if ($request->hasFile('profile_picture')){
           $image = $request->file('profile_picture');
           $file_name = "profile_".time().'.'.$image->getClientOriginalExtension();
           $image->move(base_path('uploads/profile/'),$file_name);
           $request->request->add([
            'profile_picture' => $file_name
           ]);
        }

        $affiliations_arr = $excluded_affiliations_arr = $brgy_access_arr = $hide_fields_arr = [];
        if($request->has('affiliation_access') && !empty($request->affiliation_access)) {
            $affiliations_arr = explode(";", $request->affiliation_access);
        }

        if($request->has('excluded_affiliations') && !empty($request->excluded_affiliations)) {
            $excluded_affiliations_arr = explode(";", $request->excluded_affiliations);
        }

        if($request->has('brgy_access') && !empty($request->brgy_access)) {
            $brgy_access_arr = explode(";", $request->brgy_access);
        }

        if($request->has('hide_fields') && !empty($request->hide_fields)) {
            $hide_fields_arr = explode(";", $request->hide_fields);
        }

        $restriction_properties = [
            "area" => $request->area_access, 
            "affiliations" => $affiliations_arr, 
            "excluded_affiliations" => $excluded_affiliations_arr,
            "brgy_access" => $brgy_access_arr,
            "has_export" => $request->has('has_export'),
            "bypass_update" => $request->has('bypass_update'),
            "has_activity_logs_access" => $request->has('has_activity_logs_access'),
            "has_area_search" => $request->has('has_area_search'),  
            "for_viewing_only" => $request->has('for_viewing_only'),
            "can_clear_field" => $request->has('can_clear_field'),           
            "hide_fields" => $hide_fields_arr
        ];

        $request->request->add([
            'user_type' => $request->user_type,
            'user_access' => $request->user_access,
            'restriction_properties' => json_encode($restriction_properties),
        ]);

        $user = $this->userInterface->update($id, $request->all());
        $user->syncRoles($request->user_access);

        //Prefix Output
        $user->name = $user->first_name.' '.$user->last_name;
        $user->user_type = ucwords($user->user_type);
        $user->status = $user->status == 1 ? status(_lang('Active'),'success') : status(_lang('In-Active'),'danger');
        
        if(! $request->ajax()){
           return redirect('admin/staffs')->with('success', _lang('Updated Successfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Successfully'),'data'=>$user]);
        }
    }

    public function edit($id, $request)
    { 
        $user = $this->userInterface->get($id);
        $roles = $this->roleInterface->getAll();
        
        $area_access = $affiliation_access = $excluded_affiliations = $brgy_access = $hide_fields = "";
        $has_export = $bypass_update = $has_area_search = $has_activity_logs_access = $for_viewing_only = $can_clear_field = 0;

        if(!empty($user->restriction_properties)) {
            $restriction_props = json_decode($user->restriction_properties, true);
            if(isset($restriction_props['area'])) {
                $area_access = $restriction_props['area'];
            }
            if(isset($restriction_props['affiliations'])) {
                $affiliation_access = implode(";", $restriction_props['affiliations']);
            }
            if(isset($restriction_props['excluded_affiliations'])) {
                $excluded_affiliations = implode(";", $restriction_props['excluded_affiliations']);
            }
            if(isset($restriction_props['brgy_access'])) {
                $brgy_access = implode(";", $restriction_props['brgy_access']);
            }
            if(isset($restriction_props['has_export'])) {
                $has_export = $restriction_props['has_export'];
            }
            if(isset($restriction_props['hide_fields'])) {
                $hide_fields = implode(";", $restriction_props['hide_fields']);
            }
            if(isset($restriction_props['bypass_update'])) {
                $bypass_update = $restriction_props['bypass_update'];
            }
            if(isset($restriction_props['has_activity_logs_access'])) {
                $has_activity_logs_access = $restriction_props['has_activity_logs_access'];
            }
            if(isset($restriction_props['has_area_search'])) {
                $has_area_search = $restriction_props['has_area_search'];
            }
            if(isset($restriction_props['for_viewing_only'])) {
                $for_viewing_only = $restriction_props['for_viewing_only'];
            }
            if(isset($restriction_props['can_clear_field'])) {
                $can_clear_field = $restriction_props['can_clear_field'];
            }
        }

        $passed_params = compact('user','id', 'roles', 'area_access', 'affiliation_access','excluded_affiliations','brgy_access','has_export','bypass_update', 'hide_fields', 'has_area_search', 'has_activity_logs_access', 'for_viewing_only','can_clear_field');

        if(! $request->ajax()){
           return view('backend.staff.edit', $passed_params);
        }else{
           return view('backend.staff.modal.edit', $passed_params);
        }  
    }

    private function validateCreateStaffRequest($request)
    {
        $createStaffRequest = new CreateStaffRequest();
        $rules = $createStaffRequest->rules();
        return Validator::make($request->all(), $rules);
    } 

    private function validateUpdateStaffRequest($id, $request)
    {
        $updateStaffRequest = new UpdateStaffRequest();
        $rules = $updateStaffRequest->rules($id);
        return Validator::make($request->all(), $rules);
    } 
}
