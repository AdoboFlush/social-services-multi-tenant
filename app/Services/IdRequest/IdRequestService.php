<?php

namespace App\Services\IdRequest;

use App\IdRequest;
use App\Member;
use App\Services\BaseService;
use App\Services\Member\MemberFacade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;

class IdRequestService extends BaseService
{

    private const ID_NUMBER_MIN = 000000000000;
    private const ID_NUMBER_MAX = 999999999999;

    private $memberFacade;

    public function __construct(MemberFacade $memberFacade)
    {
        $this->memberFacade = $memberFacade;
    }

    public function store(Request $request, $redirect = null)
    {
        
        try{

            $model = new IdRequest;
            $model->member_id = $request->member_id;
            $model->template_id = $request->template_id;
            $model->name_on_id =  $request->name_on_id;
            $model->status = 'Pending';
            $model->id_number = $this->generateIdNumber(str_pad($request->template_id, 4, "0", STR_PAD_LEFT));

            if(
                IdRequest::where("member_id", $request->member_id)
                    ->where("template_id", $request->template_id)
                    ->exists()
            ) {
                return back()->with('error', 'This record already exists!!');
            }

            if(!empty($request->profile_pic)){
                $encoded_image = explode(",", $request->profile_pic)[1];
                $decoded_image = base64_decode($encoded_image);
                $file_name = "profile-pic-".$request->member_id."-".date('YmdHis').".jpg";
                $profile_pic_path = public_path()."/uploads/profile/{$file_name}";
                file_put_contents($profile_pic_path, $decoded_image);
                if(file_exists($profile_pic_path)){
                    $model->profile_pic = $file_name;
                }
            }

            if(!empty($request->signature)){
                $encoded_image = explode(",", $request->signature)[1];
                $decoded_image = base64_decode($encoded_image);
                $file_name = "signature-".$request->member_id."-".date('YmdHis').".png";
                $signature_path = public_path()."/uploads/profile/{$file_name}";
                file_put_contents($signature_path, $decoded_image);
                if(file_exists($signature_path)){
                    $model->signature = $file_name;
                }
            }
            $model->remarks = $request->remarks;
            $affected_rows = $model->save();
			
			if($redirect) {
				activity("Create IdRequest from a Member")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Create IdRequest from a Member - ID : '.$request->member_id); 
				return $redirect;
			} else {
				activity("Create IdRequest")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Create IdRequest - ID : '.$request->member_id); 
				return back()->with('success', 'Record has been inserted!');
			}
        }catch(Exception $e){
            report($e);
        }

        return back()->with('error', 'Record insert failed');
        
    }

    public function update(Request $request, $redirect = null)
    { 

        try{

            // Validation here
            $model = IdRequest::find($request->id_request_id);
            $model->member_id = $request->member_id;

            if(!empty($request->template_id)){
                $model->template_id = $request->template_id;
            }      

            if(!empty($request->name_on_id)){
                $model->name_on_id =  $request->name_on_id;
            }

            if(!empty($request->remarks)){
                $model->remarks = $request->remarks;
            }

            if(!empty($request->profile_pic)){
				if($request->profile_pic === "deleted") {
					$model->profile_pic = null;
				} else {
					$encoded_image = explode(",", $request->profile_pic)[1];
					$decoded_image = base64_decode($encoded_image);
					$file_name = "profile-pic-".$request->member_id."-".date('YmdHis').".jpg";
					$profile_pic_path = public_path()."/uploads/profile/{$file_name}";
					file_put_contents($profile_pic_path, $decoded_image);
					if(file_exists($profile_pic_path)){
						$model->profile_pic = $file_name;
					}
				}
            }

            if(!empty($request->signature)){
				if($request->signature === "deleted") {
					$model->signature = null;
				} else {
					$encoded_image = explode(",", $request->signature)[1];
					$decoded_image = base64_decode($encoded_image);
					$file_name = "signature-".$request->member_id."-".date('YmdHis').".png";
					$signature_path = public_path()."/uploads/profile/{$file_name}";
					file_put_contents($signature_path, $decoded_image);
					if(file_exists($signature_path)){
						$model->signature = $file_name;
					}
				}
            }
			
            $affected_rows = $model->save();
			
			if($redirect) {
				activity("Update IdRequest from a Member")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Update IdRequest from a Member - ID : '.$request->member_id); 
				return $redirect;
			} else {
				activity("Update IdRequest")
					->causedBy(Auth::user())
					->performedOn($model)
					->withProperties($model)
					->log('Update IdRequest : '.$model->name.' | '.$model->type); 
				return back()->with('success', 'Record has been updated!');
			}
            
        }catch(Exception $e){
            report($e);
        }

        return back()->with('error', 'Record update failed');
        
    }

    public function get(string $type = '', int $parent_id = 0)
    {
        $model = new IdRequest;
        if(!empty($type)){
            $model = $model->where('type', $type);
        }
        $model = $model->where('parent_id', $parent_id);
        return $model->get();
    }

    public function getById(Request $request)
    {   
        return IdRequest::with(['member'])->find($request->id);
    }

    public function getByIdNumber(Request $request)
    {   
        return IdRequest::with(['member'])->where('id_number', $request->id)->first();
    }

    public function getAll(Request $request, $export = false, $model_only = false)
    {   
        $model = new IdRequest;
        $model = $model->with(['member','template','downloader']);
        if($request->has('filter')){
           $model = $this->filter($model, $request);
        }
        $model = $this->buildDataTableFilter($model, $request, true, ['name', 'type']);
		if($model_only){
			return $model;
		}
		if($export){
			$columns = ['id_number', 'member.full_name', 'template.name', 'member.brgy', 'remarks', 'created_at', 'updated_at'];
            return response()->streamDownload(
                function () use ($columns, $model) {
                    echo implode(",", $columns)."\r\n";
                    $model->chunk(50, function ($id_requests) use ($columns) {
                        echo $id_requests
                            ->map(fn ($id_request) => parseRowToCsv($id_request, collect($columns)))
                            ->implode("\r\n") . "\r\n";
                    });
                }
            );
		}
		$total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        return ["data" => $model->get(), "total" => $total_count];
    }

    public function updateDownloadStats($idRequest, $user = null) : bool
    {
        if(!$user) {
            $user = Auth::user();
        }
        if(!$idRequest) {
           return false; 
        }

        $affected = $idRequest->update([
            "last_downloaded_at" => Carbon::now()->format("Y-m-d H:i:s"),
            "downloaded_by" => $user->id,
            "download_count" => intval($idRequest->download_count) + 1
        ]);

        return true;
    }

    public function filter($model, $request){
        
        $request_arr = $request->all();
        foreach($request_arr['filter'] as $field => $value){
            if(!in_array($field, ['date_from', 'date_to', 'filter_field', 'filter_search', 'affiliation', 'alliance', 'show'])){
                $model = $model->where($field, 'LIKE',  $value.'%');
            }
        }
        
        if(isset($request_arr['filter']['filter_field']) && !empty($request_arr['filter']['filter_field'])){
            if($request_arr['filter']['filter_field'] == "account_number" && !empty($request_arr['filter']['filter_search'])){
                $model = $model->whereHas("member", fn ($q) => $q->where("account_number", "LIKE", "%".$request_arr['filter']['filter_search']."%") );
            }
            elseif($request_arr['filter']['filter_field'] == "first_name" && !empty($request_arr['filter']['filter_search'])){
                $model = $model->whereHas("member", fn ($q) => $q->where("first_name", "LIKE", "%".$request_arr['filter']['filter_search']."%") );
            }
            elseif($request_arr['filter']['filter_field'] == "middle_name" && !empty($request_arr['filter']['filter_search'])){
                $model = $model->whereHas("member", fn ($q) => $q->where("middle_name", "LIKE", "%".$request_arr['filter']['filter_search']."%") );
            }
            elseif($request_arr['filter']['filter_field'] == "last_name" && !empty($request_arr['filter']['filter_search'])){
                $model = $model->whereHas("member", fn ($q) => $q->where("last_name", "LIKE", "%".$request_arr['filter']['filter_search']."%") );
            }
            elseif($request_arr['filter']['filter_field'] == "brgy" && !empty($request_arr['filter']['filter_search'])){
                $model = $model->whereHas("member", fn ($q) => $q->where("brgy", "LIKE", "%".$request_arr['filter']['filter_search']."%") );
            }
        }

        if(isset($request_arr['filter']['affiliation']) && !empty($request_arr['filter']['affiliation'])) {
            $model = $model->whereHas("member", fn ($q) => $q->where("affiliation", "LIKE", "%".$request_arr['filter']['affiliation']."%") );
        }

        if(isset($request_arr['filter']['alliance']) && !empty($request_arr['filter']['alliance'])) {
            $model = $model->whereHas("member", fn ($q) => $q->where("alliance", "LIKE", "%".$request_arr['filter']['alliance']."%") );
        }

        if(isset($request_arr['filter']['show']) && !empty($request_arr['filter']['show'])) {
            if($request_arr['filter']['show'] === "not_downloaded") {
                $model = $model->where("download_count", "<=", 0);
            } else if($request_arr['filter']['show'] === "downloaded") {
                $model = $model->where("download_count", ">", 0);
            }
        }
    
        if(isset($request_arr['filter']['date_from'])){
            if(isset($request_arr['filter']['date_to'])){
                $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from']." 00:00:00", $request_arr['filter']['date_to']." 23:59:59"]);
            }else{
                $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from']." 00:00:00", $request_arr['filter']['date_from']." 23:59:59"]);
            }
        }

        return $model;
    }

    public function getIdRequests(string $type)
    {
        $model = new IdRequest;
        $model = $model->where('type', $type)->where('status', 1);
        return $model->pluck('name');
    }

    public function generateIdNumber(string $idNumberPrefix)
    {
        $idNumber = $idNumberPrefix . random_int(self::ID_NUMBER_MIN, self::ID_NUMBER_MAX);
        if($this->checkIdNumberIfExists($idNumber)) {
            $this->generateIdNumber($idNumberPrefix);
        } else {
            return $idNumber;
        }
    }

    private function checkIdNumberIfExists(string $id_number)
    {
        return IdRequest::where('id_number', $id_number)->exists();
    }

    public function deleteMultiple(Request $request)
    {
        if($request->has('selected_ids')){
            foreach($request->selected_ids as $selected_id){
                $model = IdRequest::find($selected_id);
                activity("Delete IdRequest")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Delete IdRequest ID : '.$selected_id); 
                $model->delete();
            }
            return 1;
        }
        return 0;
    }

}