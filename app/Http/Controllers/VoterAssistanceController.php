<?php

namespace App\Http\Controllers;

use App\AssistanceEvent;
use App\IdRequest;
use App\Member;
use App\Services\Member\MemberFacade;
use App\Services\MemberCode\MemberCodeFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Voter\VoterFacade;
use App\Services\Tag\TagFacade;
use App\Traits\ActivityLog\LogsChanges;
use App\Voter;
use App\VoterHasAssistance;
use App\VoterTagDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class VoterAssistanceController extends Controller
{

    use LogsChanges;

    protected $voterFacade;
    protected $tagFacade;
    protected $memberFacade;
    protected $memberCodeFacade;

    public function __construct(VoterFacade $voterFacade, TagFacade $tagFacade, MemberFacade $memberFacade, MemberCodeFacade $memberCodeFacade)
    {
        $this->voterFacade = $voterFacade;
        $this->tagFacade = $tagFacade;
        $this->memberFacade = $memberFacade;
        $this->memberCodeFacade = $memberCodeFacade;
    }

    public function showAssistanceEventIndex(Request $request, AssistanceEvent $assistance_event)
    {
        $brgy_access = $hide_fields = [];
        if ($request->ajax()) {

            $voters = $this->voterFacade::getAllVoterTagDetails($request, false, $assistance_event);

            return response()->json([
                'data' => $voters["data"],
                'recordsTotal' => $voters["count"],
                'recordsFiltered' => $voters["count"],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        } else {

            $brgys = $this->tagFacade::getTags('brgy');
            $religions = $this->tagFacade::getTags('religion');
            $purposes = $this->tagFacade::getTags('purpose');
            $civil_statuses = $this->tagFacade::getTags('civil_status');
            $beneficiaries = $this->tagFacade::getTags('beneficiaries');
            $alliances = $this->tagFacade::get('alliance', 0);
            $alliances_1 = $this->tagFacade::getByCustomField('alliance_1', $brgy_access);
            $affiliations = $this->tagFacade::get('affiliation', 0);
            $affiliations_1 = $this->tagFacade::get('affiliation_1', 0);
            $sectorals = $this->tagFacade::get('sectoral', 0);
            $sectorals_1 = $this->tagFacade::getTags('sectoral');
            $organizations = $this->tagFacade::getTags('organization');
            $party_lists = $this->tagFacade::getTags('party_list');

            return view(
                'backend.voter_assistance.event_details',
                compact(
                    'brgys',
                    'religions',
                    'purposes',
                    'civil_statuses',
                    'beneficiaries',
                    'alliances',
                    'alliances_1',
                    'affiliations',
                    'affiliations_1',
                    'sectorals',
                    'sectorals_1',
                    'organizations',
                    'assistance_event',
                )
            );
        }
    }

    public function assistanceEventIndex(Request $request)
    {
        if ($request->ajax()) {

            $assistance_event = AssistanceEvent::when($request->name, fn($q) => $q->where("name", "LIKE", "%{$request->name}%"))
                ->when($request->description, fn($q) => $q->where("description", "LIKE", "%{$request->description}%"));

            $total = $assistance_event->count();
            $data = $assistance_event->withCount(["assistances"])->offset($request->start)->limit($request->length)->get();
               
            return response()->json([
                'data' => $data,
                'recordsTotal' =>  $total,
                'recordsFiltered' =>  $total,
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
            
        } else {

            $brgys = $this->tagFacade::getTags('brgy');
            $religions = $this->tagFacade::getTags('religion');
            $purposes = $this->tagFacade::getTags('purpose');
            $civil_statuses = $this->tagFacade::getTags('civil_status');
            $beneficiaries = $this->tagFacade::getTags('beneficiaries');
            $alliances = $this->tagFacade::get('alliance', 0);
            $alliances_1 = $this->tagFacade::getByCustomField('alliance_1', []);
            $affiliations = $this->tagFacade::get('affiliation', 0);
            $affiliations_1 = $this->tagFacade::get('affiliation_1', 0);
            $sectorals = $this->tagFacade::get('sectoral', 0);
            $sectorals_1 = $this->tagFacade::getTags('sectoral');
            $organizations = $this->tagFacade::getTags('organization');
            $party_lists = $this->tagFacade::getTags('party_list');

            return view(
                'backend.voter_assistance.events',
                compact(
                    'brgys',
                    'religions',
                    'purposes',
                    'civil_statuses',
                    'beneficiaries',
                    'alliances',
                    'alliances_1',
                    'affiliations',
                    'affiliations_1',
                    'sectorals',
                    'sectorals_1',
                    'organizations',
                )
            );
        }
    }

    public function createAssistanceEvent(Request $request)
    {
        return view('backend.voter_assistance.modal.create_event');
    }

    public function storeAssistanceEvent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assistance_type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'amount' => 'required|numeric|min:0',
            'custom_condition_props' => 'nullable|string',
        ]);

        try {
            AssistanceEvent::create([
                'name' => $request->name,
                'description' => $request->description,
                'starts_at' => Carbon::parse($request->starts_at)->toDateTimeString(),
                'ends_at' => Carbon::parse($request->ends_at)->toDateTimeString(),
                'assistance_type' => $request->assistance_type,
                'is_active' => $request->is_active,
                'amount' => $request->amount,
                'custom_condition_props' => $request->custom_condition_props,
            ]);        

        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        return response()->json([
            'result'=>'success',
            'action'=>'create', 
            'message' => _lang('Assistance event created successfully.')
        ]);
    }

    public function editAssistanceEvent(Request $request, AssistanceEvent $assistance_event)
    {
        return view('backend.voter_assistance.modal.edit_event', compact('assistance_event'));
    }

    public function updateAssistanceEvent(Request $request, AssistanceEvent $assistance_event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assistance_type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'amount' => 'required|numeric|min:0',
            'custom_condition_props' => 'nullable|string',
        ]);

        $assistance_event->update([
            'name' => $request->name,
            'description' => $request->description,
            'starts_at' => Carbon::parse($request->starts_at)->toDateTimeString(),
            'ends_at' => Carbon::parse($request->ends_at)->toDateTimeString(),
            'assistance_type' => $request->assistance_type,
            'is_active' => $request->is_active,
            'amount' => $request->amount,
            'custom_condition_props' => $request->custom_condition_props,
        ]);

        return response()->json([
            'result'=>'success',
            'action'=>'update', 
            'message' => _lang('Assistance event updated successfully.')
        ]);
    }

    public function claimAssistanceByQR(Request $request, AssistanceEvent $assistance_event)
    {

        $response  = [
            'result'=>'error',
            'action'=>'claim', 
            'message' => 'unexpected error occured.'
        ];

        try {

            $qr_value = Crypt::decryptString($request->qr_value);
            if($request->has('mode') && $request->mode == 'member-id') {
                $id_number = $qr_value;
                $id_request = IdRequest::with(['member.voter'])->where("id_number", $id_number)->first();
                if(!$id_request) {
                    throw new Exception("Member ID not found.");
                }
                $voter_id = $id_request->member->voter->id;
            } else {
                $parse_value = explode("|", $qr_value);
                $event_id = @$parse_value[0];
                $voter_id = @$parse_value[1];
                $voter = VoterTagDetail::find($voter_id);
            }

            $voter = VoterTagDetail::find($voter_id);

            // if($event_id != $assistance_event->id) {
            //     throw new Exception("Invalid event id.");
            // }
            
            if(!$voter) {
                throw new Exception("Voter not found.");
            }

            if($voter->is_deceased) {
                throw new Exception("Voter is already deceased.");
            }

            $assistance_count = VoterHasAssistance::where("assistance_event_id", $assistance_event->id)
                ->where("voter_tag_detail_id", $voter->id)
                ->count();
            if($assistance_count > 0) {
                throw new Exception("Assistance already claimed.");
            }
            
            $voter->claimEventAssistance($assistance_event);
            $response['result'] = 'success';
            $response['message'] = 'Assistance claimed successfully.';

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response["message"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function claimAssistance(Request $request, AssistanceEvent $assistance_event)
    {
        $response  = [
            'result'=>'error',
            'action'=>'claim', 
            'message' => 'unexpected error occured.'
        ];

        try {
            $event_id = $assistance_event->id;
            $voter_id = $request->voter_id;

            $voter = VoterTagDetail::find($voter_id);
            if(!$voter) {
                throw new Exception("Voter not found.");
            }

            if($voter->is_deceased) {
                throw new Exception("Voter is already deceased.");
            }

            $assistance_count = VoterHasAssistance::where("assistance_event_id", $event_id)
                ->where("voter_tag_detail_id", $voter_id)
                ->count();
            if($assistance_count > 0) {
                throw new Exception("Assistance already claimed.");
            }
            
            $voter->claimEventAssistance($assistance_event);
            $response['result'] = 'success';
            $response['message'] = 'Assistance claimed successfully.';

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response["message"] = $e->getMessage();
        }

        return response()->json($response);

    }

    public function generateMultipleCoupons(Request $request, AssistanceEvent $assistance_event)
    {
        $selected_ids = explode(",", $request->selected_ids);
        $voters = VoterTagDetail::whereIn("id", $selected_ids)->get();
        $last_page = $request->last_page;
        $file_name = "coupon-batch-{$request->batch_number}";
        return view('backend.voter_assistance.template.coupon_per_page', compact('voters', 'file_name', 'last_page', 'assistance_event'));
    }

    // FOR PAYMASTER

    public function showAssistanceEventPayMasterIndex(Request $request, AssistanceEvent $assistance_event)
    {
        $brgy_access = $hide_fields = [];
        if ($request->ajax()) {

            $voters = $this->voterFacade::getAllVoterTagDetails($request, false, $assistance_event);

            return response()->json([
                'data' => $voters["data"],
                'recordsTotal' => $voters["count"],
                'recordsFiltered' => $voters["count"],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        } else {

            $brgys = $this->tagFacade::getTags('brgy');
            $religions = $this->tagFacade::getTags('religion');
            $purposes = $this->tagFacade::getTags('purpose');
            $civil_statuses = $this->tagFacade::getTags('civil_status');
            $beneficiaries = $this->tagFacade::getTags('beneficiaries');
            $alliances = $this->tagFacade::get('alliance', 0);
            $alliances_1 = $this->tagFacade::getByCustomField('alliance_1', $brgy_access);
            $affiliations = $this->tagFacade::get('affiliation', 0);
            $affiliations_1 = $this->tagFacade::get('affiliation_1', 0);
            $sectorals = $this->tagFacade::get('sectoral', 0);
            $sectorals_1 = $this->tagFacade::getTags('sectoral');
            $organizations = $this->tagFacade::getTags('organization');
            $party_lists = $this->tagFacade::getTags('party_list');

            return view(
                'guest.voter_assistance.event_details',
                compact(
                    'brgys',
                    'religions',
                    'purposes',
                    'civil_statuses',
                    'beneficiaries',
                    'alliances',
                    'alliances_1',
                    'affiliations',
                    'affiliations_1',
                    'sectorals',
                    'sectorals_1',
                    'organizations',
                    'assistance_event',
                )
            );
        }
    }

    public function assistanceEventPayMasterIndex(Request $request)
    {
        if ($request->ajax()) {

            $assistance_event = AssistanceEvent::when($request->name, fn($q) => $q->where("name", "LIKE", "%{$request->name}%"))
                ->when($request->description, fn($q) => $q->where("description", "LIKE", "%{$request->description}%"));

            $total = $assistance_event->count();
            $data = $assistance_event->withCount(["assistances"])->offset($request->start)->limit($request->length)->get();
               
            return response()->json([
                'data' => $data,
                'recordsTotal' =>  $total,
                'recordsFiltered' =>  $total,
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
            
        } else {

            $brgys = $this->tagFacade::getTags('brgy');
            $religions = $this->tagFacade::getTags('religion');
            $purposes = $this->tagFacade::getTags('purpose');
            $civil_statuses = $this->tagFacade::getTags('civil_status');
            $beneficiaries = $this->tagFacade::getTags('beneficiaries');
            $alliances = $this->tagFacade::get('alliance', 0);
            $alliances_1 = $this->tagFacade::getByCustomField('alliance_1', []);
            $affiliations = $this->tagFacade::get('affiliation', 0);
            $affiliations_1 = $this->tagFacade::get('affiliation_1', 0);
            $sectorals = $this->tagFacade::get('sectoral', 0);
            $sectorals_1 = $this->tagFacade::getTags('sectoral');
            $organizations = $this->tagFacade::getTags('organization');
            $party_lists = $this->tagFacade::getTags('party_list');

            return view(
                'guest.voter_assistance.events',
                compact(
                    'brgys',
                    'religions',
                    'purposes',
                    'civil_statuses',
                    'beneficiaries',
                    'alliances',
                    'alliances_1',
                    'affiliations',
                    'affiliations_1',
                    'sectorals',
                    'sectorals_1',
                    'organizations',
                )
            );
        }
    }

    public function deleteMultipleAssistanceEvents(Request $request)
    {
        $response = [
            'result' => 'error',
            'message' => 'Unexpected error occurred.'
        ];

        try {
            $ids = $request->input('selected_ids', []);
            if (!is_array($ids) || empty($ids)) {
                throw new \Exception('No event IDs provided.');
            }

            foreach ($ids as $eventId) {
                // Delete related VoterHasAssistance
                VoterHasAssistance::where('assistance_event_id', $eventId)->delete();
                // Delete the AssistanceEvent
                AssistanceEvent::where('id', $eventId)->delete();
            }

            $response['result'] = 'success';
            $response['message'] = 'Selected events and their related assistances have been deleted.';
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function getEventStats(Request $request, AssistanceEvent $assistance_event)
    {
        $claimed = VoterHasAssistance::where('assistance_event_id', $assistance_event->id)->count();
        $total = $this->voterFacade::getAllVoterTagDetails($request, false, $assistance_event, true);
        
        return response()->json([
            'total' => $total,
            'claimed' => $claimed,
            'percentage' => $total > 0 ? round(($claimed / $total) * 100, 2) : 0
        ]);
    }
}
