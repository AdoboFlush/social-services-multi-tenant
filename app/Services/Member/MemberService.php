<?php

namespace App\Services\Member;

use App\Member;
use App\Services\BaseService;
use App\Services\MemberCode\MemberCodeFacade;
use App\Traits\Exportable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MemberService extends BaseService
{

    use Exportable;

    const ACCOUNT_NUMBER_MIN = 1000000000;
    const ACCOUNT_NUMBER_MAX = 9999999999;

    protected $memberCodeFacade;

    public function __construct(MemberCodeFacade $memberCodeFacade)
    {
        $this->memberCodeFacade = $memberCodeFacade;
    }

    public function generateAccountNumber()
    {
        $accountNumberPrefix = 'AA-';
        $accountNumber = $accountNumberPrefix . random_int(self::ACCOUNT_NUMBER_MIN, self::ACCOUNT_NUMBER_MAX);
        if($this->checkAccountNumberIfExists($accountNumber)) {
            $this->generateAccountNumber();
        } else {
            return $accountNumber;
        }
    }

    private function checkAccountNumberIfExists(string $accountNumber)
    {
        return Member::where('account_number', $accountNumber)->exists();
    }

    public function store(Request $request)
    {
        
        try{

            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'brgy' => 'required',
                'address' => 'required',
                'birth_date' => 'required',
                'brgy' => 'required',
                'gender' => 'required',
                // 'contact_person_first_name' => 'required',
                // 'contact_person_last_name' => 'required',
                // 'contact_person_number' => 'required',
                // 'contact_person_address' => 'required',
            ]);

            if ($validator->fails()) {
                $message = _lang(($validator->errors()->first()));
                return back()->withInput()->with('error', $message);
            }

            $model = new Member;
            $model->account_number = $this->generateAccountNumber();
            $model->first_name = $request->first_name;
            $model->last_name = $request->last_name;
            $model->middle_name = !empty($request->middle_name) ? $request->middle_name : '';
            $model->suffix = !empty($request->suffix) ? $request->suffix : '';
            $model->birth_date = !empty($request->birth_date) ? $request->birth_date : '';
            $model->address = !empty($request->address) ? $request->address : '';
            $model->gender = !empty($request->gender) ? $request->gender : '';
            $model->precinct = !empty($request->precinct) ? $request->precinct : '';
            $model->brgy = !empty($request->brgy) ? $request->brgy : '';
            $model->alliance = !empty($request->alliance) ? $request->alliance : '';
            $model->affiliation = !empty($request->affiliation) ? $request->affiliation : '';
            $model->civil_status = !empty($request->civil_status) ? $request->civil_status : '';
            $model->religion = !empty($request->religion) ? $request->religion : '';
            $model->contact_number = !empty($request->contact_number) ? $request->contact_number : '';
            $model->remarks = !empty($request->remarks) ? $request->remarks : '';
            $model->is_voter = !empty($request->is_voter) ? $request->is_voter : 0;
            $model->parent_id = !empty($request->parent_id) ? $request->parent_id : 0;
            $model->status = 1;
            $model->contact_person_first_name = !empty($request->contact_person_first_name) ? $request->contact_person_first_name : '';
            $model->contact_person_last_name = !empty($request->contact_person_last_name) ? $request->contact_person_last_name : '';
            $model->contact_person_middle_name = !empty($request->contact_person_middle_name) ? $request->contact_person_middle_name : '';
            $model->contact_person_suffix = !empty($request->contact_person_suffix) ? $request->contact_person_suffix : '';
            $model->contact_person_number = !empty($request->contact_person_number) ? $request->contact_person_number : '';
            $model->contact_person_address = !empty($request->contact_person_address) ? $request->contact_person_address : '';
            $affected_rows = $model->save();

             // create a member code
             $this->memberCodeFacade::generateMemberCode($model->id);

            activity("Create Member")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Create Member : '.$model->account_number); 
            return back()->with('success', 'Record has been inserted!');

        }catch (QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                report($e);
                $message = "Duplicate Entry.";
            }
        }catch(Exception $e){
            report($e);
            $message = $e->getMessage();
        }

        return back()->with('error', 'Record insert failed - '.$message);
        
    }

    public function update(Request $request)
    { 

        try{

            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'brgy' => 'required',
                'address' => 'required',
                'birth_date' => 'required',
                'brgy' => 'required',
                'gender' => 'required',
                // 'contact_person_first_name' => 'required',
                // 'contact_person_last_name' => 'required',
                // 'contact_person_number' => 'required',
                // 'contact_person_address' => 'required',
            ]);

            if ($validator->fails()) {
                $message = _lang(($validator->errors()->first()));
                return back()->withInput()->with('error', $message);
            }
            
            $model = Member::find($request->update_id);
            if(!$model){
                return redirect('id_system/members')->with('error', 'Record not found. Update failed');
            }
            $model->first_name = $request->first_name;
            $model->last_name = $request->last_name;
            $model->middle_name = !empty($request->middle_name) ? $request->middle_name : '';
            $model->suffix = !empty($request->suffix) ? $request->suffix : '';
            $model->birth_date = !empty($request->birth_date) ? $request->birth_date : '';
            $model->address = !empty($request->address) ? $request->address : '';
            $model->gender = !empty($request->gender) ? $request->gender : '';
            $model->precinct = !empty($request->precinct) ? $request->precinct : '';
            $model->brgy = !empty($request->brgy) ? $request->brgy : '';
            $model->alliance = !empty($request->alliance) ? $request->alliance : '';
            $model->affiliation = !empty($request->affiliation) ? $request->affiliation : '';
            $model->civil_status = !empty($request->civil_status) ? $request->civil_status : '';
            $model->religion = !empty($request->religion) ? $request->religion : '';
            $model->contact_number = !empty($request->contact_number) ? $request->contact_number : '';
            $model->remarks = !empty($request->remarks) ? $request->remarks : '';
            $model->contact_person_first_name = !empty($request->contact_person_first_name) ? $request->contact_person_first_name : '';
            $model->contact_person_last_name = !empty($request->contact_person_last_name) ? $request->contact_person_last_name : '';
            $model->contact_person_middle_name = !empty($request->contact_person_middle_name) ? $request->contact_person_middle_name : '';
            $model->contact_person_suffix = !empty($request->contact_person_suffix) ? $request->contact_person_suffix : '';
            $model->contact_person_number = !empty($request->contact_person_number) ? $request->contact_person_number : '';
            $model->contact_person_address = !empty($request->contact_person_address) ? $request->contact_person_address : '';
            $model->status = 1;
            $affected_rows = $model->save();

            activity("Update Member")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Update Member : '.$model->account_number); 
            return back()->with('success', 'Record has been updated!');

        }catch (QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                report($e);
                $message = "Duplicate Entry";
            }
        }catch(Exception $e){
            report($e);
            $message = $e->getMessage();
        }

        return back()->with('error', 'Record insert failed - '.$message);
        
    }

    public function get(string $account_number = '')
    {
        $model = new Member;
        if(!empty($account_number)){
            $model = $model->where('account_number', $account_number);
        }
        $model = $model->where('status', 1);
        return $model->get();
    }

    public function getById(Request $request)
    {   
        return Member::with(['id_requests'])->find($request->id);
    }

    public function getAll(Request $request)
    {   
        $model = new Member;
        $model = $model->with('member_code', 'voter');

        if($request->has('filter')){
            $request_arr = $request->all();
            foreach($request_arr['filter'] as $field => $value){
                if(in_array($field, Member::VOTER_FIELDS)) {
                    $model = $model->whereHas('voter', fn ($q) => $q->where($field, 'LIKE',  $value.'%'));
                } else {
                    $model = $model->where($field, 'LIKE',  $value.'%');
                }
            }
        }
        $model = $this->buildDataTableFilter($model, $request, true, ['first_name', 'last_name']);
        $model = $this->buildModelQueryDataTable($model, $request, ['full_name' => 'last_name']);
        return $model->get();
    }

    public function export(Request $request)
    {
        return $this->exportToCSV(
            function () use ($request) {
                $model = new Member;
                $model = $model->with('member_code');
                if($request->has('filter')){
                    $request_arr = $request->all();
                    foreach($request_arr['filter'] as $field => $value){
                        if(in_array($field, Member::VOTER_FIELDS)) {
                            $model = $model->whereHas('voter', fn ($q) => $q->where($field, 'LIKE',  $value.'%'));
                        } else {
                            $model = $model->where($field, 'LIKE',  $value.'%');
                        }
                    }
                }
                $model = $this->buildDataTableFilter($model, $request, true, ['first_name', 'last_name']);
                $model = $this->buildModelQueryDataTable($model, $request, ['full_name' => 'last_name']);
                return $model;
            },
            collect([
                "Full Name" => fn ($a) => $this->escapeComma($a->full_name),
                "Member Number" => fn ($a) => $a->account_number,
                "Birth Date" => fn ($a) => $a->birth_date,
                "Gender" => fn ($a) => $a->gender,
                "Precinct" => fn ($a) => $a->precinct,
                "Address" => fn ($a) => $this->escapeComma($a->address),
                "Barangay" => fn ($a) => $a->brgy,
                "Alliance" => fn ($a) => $a->alliance,
                "Affiliation" => fn ($a) => $a->affiliation,
                "Religion" => fn ($a) => $a->religion,
                "Civil Status" => fn ($a) => $a->civil_status,
                "Contact Number" => fn ($a) => $a->contact_number,
                "Code" => fn ($a) => $a->code,
                "Remarks" => fn ($a) => $this->escapeComma($a->remarks),
            ]),
        );
    }
	
	public function getMembersWithoutId($template_id)
	{
		return Member::whereHas("id_requests.template", fn ($q) => $q->where("id", $template_id));
	}

    public function getMembers(string $type)
    {
        $model = new Member;
        $model = $model->where('type', $type)->where('status', 1);
        return $model->pluck('name');
    }

    public function getTotalCount(Request $request)
    {   
        $model = new Member;
        if($request->has('filter')){
            $request_arr = $request->all();
            foreach($request_arr['filter'] as $field => $value){
                if(in_array($field, Member::VOTER_FIELDS)) {
                    $model = $model->whereHas('voter', fn ($q) => $q->where($field, 'LIKE',  $value.'%'));
                } else {
                    $model = $model->where($field, 'LIKE',  $value.'%');
                }
            }
        }
        $model = $this->buildDataTableFilter($model, $request, true, ['first_name', 'last_name']);
        return $model->count();
    }

    public function deleteMultiple(Request $request)
    {
        if($request->has('selected_ids')){
            foreach($request->selected_ids as $selected_id){
                $model = Member::withCount('id_requests')->find($selected_id);
                if($model && $model->id_requests_count > 0) {
                    continue; // if there are existing id_requests, skip deletion
                }
                activity("Delete Member")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Delete Member ID : '.$selected_id); 
                $model->delete();
            }
            return 1;
        }
        return 0;
    }

    public function search(Request $request)
    {
        $model = new Member();
        if($request->has('q')){
            $model = $model->where(DB::raw("CONCAT(`last_name`, ', ', `first_name`, ' ', `middle_name`)"), 'LIKE', '%' . $request->q . '%');
        }
        if($request->has('page')){
            $offset = 0;
            $limit = 10;
            if($request->page > 1){
                $offset +=  $limit * $request->page;
                $model = $model->offset($offset)->limit($limit);
            }
        }
        $model = $model->get()->map(function($data){
            return [ 'id' => $data->id, 'text' => "({$data->account_number}) {$data->full_name}"];
        });
        return $model;
    }

}