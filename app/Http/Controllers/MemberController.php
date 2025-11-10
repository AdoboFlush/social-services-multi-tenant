<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberResetPasswordRequest;
use App\IdRequest;
use App\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Services\IdRequest\IdRequestFacade;
use App\Services\Member\MemberFacade;
use App\Services\Tag\TagFacade;
use App\Services\Template\TemplateFacade;
use App\Services\User\UserFacade;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;

class MemberController extends Controller
{
    protected $memberFacade;
    protected $tagFacade;
    protected $templateFacade;
    protected $idRequestFacade;
    protected $userFacade;

	public function __construct(    
        TagFacade $tagFacade,
        MemberFacade $memberFacade, 
        TemplateFacade $templateFacade, 
        IdRequestFacade $idRequestFacade,
        UserFacade $userFacade)
	{
        $this->memberFacade = $memberFacade;
        $this->tagFacade =  $tagFacade;
        $this->templateFacade =  $templateFacade;
        $this->idRequestFacade =  $idRequestFacade;
        $this->userFacade = $userFacade;
	}

    public function index(Request $request)
    {
        if($request->ajax()){
            $data = $this->memberFacade::getAll($request);
            $total = $this->memberFacade::getTotalCount($request);
            return response()->json([
                'data'=> $data,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{
            return view('backend.id_system.member.list');
        }
    }

    public function get(Request $request)
    {
        return response()->json($this->memberFacade::getById($request));
    }

    public function export(Request $request)
    {
        return MemberFacade::export($request);
    }

    public function search(Request $request)
    {
        return response()->json($this->memberFacade::search($request));
    }

    public function importToRequests(Request $request) 
    {
        if($request->isMethod('POST')) {

            if(empty($request->filter_field) || empty($request->filter_search)){
                return redirect('members')
                    ->withError("Must filter the records to avoid connection timeout");
            }

            $members = Member::when(!empty($request->filter_field) && !empty($request->filter_search), 
                fn ($q) => !in_array($request->filter_field, Member::VOTER_FIELDS) 
                    ? $q->where($request->filter_field, $request->filter_search) 
                    : $q->whereHas('voter', fn ($sq) => $sq->where($request->filter_field, $request->filter_search)
                ))->get();

            $totalCount = $inserted = $dup = $failed = 0;
			
			set_time_limit(300);
			
            foreach($members as $member){
                try{

                    if(
                        IdRequest::where("member_id", $member->id)
                            ->where("template_id", $request->template_id)
                            ->exists()
                    ) {
                        $dup++;
                    } else {
                        $model = new IdRequest();
                        $model->member_id = $member->id;
                        $model->template_id = $request->template_id;
                        $model->name_on_id =  $member->full_name;
                        $model->status = 'Pending';
                        $model->remarks = 'Created via Bulk Member';
                        $model->id_number = $this->idRequestFacade::generateIdNumber(str_pad($request->template_id, 4, "0", STR_PAD_LEFT));
                        $affected_rows = $model->save();

                        $inserted++;
                        activity("Create New Request via Bulk")
                                ->causedBy(Auth::user())
                                ->performedOn($model)
                                ->withProperties($model)
                                ->log('Create Member : '.$model->account_number); 
                    }
                            
                }catch(Exception $e){
                    $failed++;
                }
                $totalCount++;
            };

            return redirect('id_system/members')
                ->withSuccess("Done ID requests creation process")
                ->withMessages([
                    "Total Voters: {$totalCount}",
                    "Inserted: {$inserted}",
                    "Already exists in Members: {$dup}",
                    "Failed: {$failed}",
                ]); 

        } else {
            $templates = $this->templateFacade::get();
            return view('backend.id_system.member.modal.import_to_requests', compact('templates'));
        }
    }

    public function create(Request $request)
    {
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::getTags('purpose');
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $beneficiaries = $this->tagFacade::getTags('beneficiaries');
        return view('backend.id_system.member.create', compact('brgys','alliances','affiliations','religions','purposes','civil_statuses', 'beneficiaries'));
    }

    public function store(Request $request)
    {
        return $this->memberFacade::store($request);
    }

    public function edit(Request $request)
    {
        $data = $this->memberFacade::getById($request);
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::getTags('purpose');
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $beneficiaries = $this->tagFacade::getTags('beneficiaries');
        return view('backend.id_system.member.edit', compact('data', 'brgys','alliances','affiliations','religions','purposes','civil_statuses', 'beneficiaries'));
    }

    public function update(Request $request)
    {
        return $this->memberFacade::update($request);
    }

    public function show(Request $request)
    {
        $data = $this->memberFacade::getById($request);
        if($data) {
            $data = $data->load("voter");
        }
        $data = $data->toArray();
        return view('backend.id_system.member.modal.show', compact('data'));
    }

    public function delete(Request $request)
    {
        return response($this->memberFacade::deleteMultiple($request));
    }
    
    public function resetPassword(MemberResetPasswordRequest $request)
    {
       if ($this->userFacade::resetMemberPassword($request)){
        return response()->json(['result' => 'success']);
       }else{
        return response()->json(['result' => 'error']);
       }
    }
}
