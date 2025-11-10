<?php

namespace App\Http\Controllers;

use App\Attendee;
use App\Event;
use App\Member;
use App\Services\Event\EventFacade;
use App\Services\SocialServiceAssistance\SocialServiceAssistanceFacade;
use App\Services\Tag\TagFacade;
use App\SocialServiceAssistance;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    private $eventFacade;
    private $tagFacade;
    private $socialServiceAssistanceFacade;

    public function __construct(
        EventFacade $eventFacade,
        TagFacade $tagFacade,
        SocialServiceAssistanceFacade $socialServiceAssistanceFacade
    ) {
        $this->eventFacade = $eventFacade;
        $this->tagFacade = $tagFacade;
        $this->socialServiceAssistanceFacade = $socialServiceAssistanceFacade;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $event = $this->eventFacade::getAll($request);
            return response()->json([
                'data' => $event["data"],
                'recordsTotal' => $event["total"],
                'recordsFiltered' => $event["total"],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        } else {
            $events = Event::whereRaw("YEAR(start_at) >= ?", [date('Y')])
                ->where("active", 1)
                ->get();
            return view('backend.event.list', compact('events'));
        }
    }

    public function create(Request $request)
    {
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::get('purpose', 0); 
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $beneficiaries = $this->tagFacade::getTags('beneficiaries');
        return view('backend.event.create', compact('brgys', 'alliances', 'affiliations', 'religions', 'purposes', 'civil_statuses', 'beneficiaries'));
    }

    public function createAttendee(Request $request)
    {
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::getTags('purpose');
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $beneficiaries = $this->tagFacade::getTags('beneficiaries');
        $event = Event::find($request->event_id);
        return view('backend.event.attendee.create', compact('event', 'brgys', 'alliances', 'affiliations', 'religions', 'purposes', 'civil_statuses', 'beneficiaries'));
    }

    public function storeAttendee(Request $request)
    {
        return $this->eventFacade::storeAttendee($request);
    }


    public function store(Request $request)
    {
        return $this->eventFacade::store($request);
    }

    public function edit(Request $request)
    {
        $data = Event::find($request->id);
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::get('purpose', 0);
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $beneficiaries = $this->tagFacade::getTags('beneficiaries');
        return view('backend.event.edit', compact('data', 'brgys', 'alliances', 'affiliations', 'religions', 'purposes', 'civil_statuses', 'beneficiaries'));
    }

    public function editAttendee(Request $request)
    {
        $data = Attendee::find($request->id);
        $event = Event::find($data->event_id);
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::getTags('purpose');
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $beneficiaries = $this->tagFacade::getTags('beneficiaries');
        return view('backend.event.attendee.edit', compact('data', 'event', 'brgys', 'alliances', 'affiliations', 'religions', 'purposes', 'civil_statuses', 'beneficiaries'));
    }

    public function update(Request $request)
    {
        return $this->eventFacade::update($request);
    }

    public function updateAttendee(Request $request)
    {
        return $this->eventFacade::updateAttendee($request);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $event = $this->eventFacade::getAllAttendees($request);
            return response()->json([
                'data' => $event["data"],
                'recordsTotal' => $event["total"],
                'recordsFiltered' => $event["total"],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        } else {
            $event_id = $request->id;
            $event = Event::find($event_id);
            $event_title = $event->name . " (" . $event->start_at . " - " . substr($event->end_at, 11, 9) . ")";

            $event = $event->load("attendees");
            $data = $event->toArray();
            $data["total_attendees"] = $event->attendees()->count();

            return view('backend.event.show', compact('data', 'event_id', 'event_title'));
        }
    }

    public function showAttendee(Request $request)
    {
        $data = Attendee::find($request->id)->toArray();
        $socialServices = $this->socialServiceAssistanceFacade::getAssistanceHistory([
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'middle_name' => trim($data['middle_name']),
            'suffix' => trim($data['suffix']),
            'birth_date' => trim($data['birth_date']),
        ]);

        return view('backend.event.attendee.show', compact('data', 'socialServices'));
    }

    public function delete(Request $request)
    {
        return response($this->eventFacade::deleteMultiple($request));
    }

    public function deleteAttendee(Request $request)
    {
        return response($this->eventFacade::deleteMultipleAttendee($request));
    }

    public function releaseAssistance(Request $request)
    {
        if ($request->has('selected_ids')) {
            foreach ($request->selected_ids as $selected_id) {
            
                $attendee = Attendee::find($selected_id);
                $event = Event::find($attendee->event_id);                
                
                $response = $this->socialServiceAssistanceFacade::createFromAttendee($event, $attendee);
                if ($response['status'] === 1){
                    $full_name = $attendee->last_name. "," . $attendee->first_name . " " . $attendee->middle_name . " " . $attendee->suffix;
                    Log::info("Social-Service-Assistance-Insert-Success " . $event->request_type . " " . $full_name);
                }else{
                    $full_name = $attendee->last_name. "," . $attendee->first_name . " " . $attendee->middle_name . " " . $attendee->suffix;
                    Log::error("Social-Service-Assistance-Insert-Failed " . $event->request_type . " " . $full_name);
                }                           
            }

            return 1;
        }

        return 0;
    }

    public function createAttendeeFromMember(Event $event, Request $request) 
    {
        $member = Member::find($request->member_id);
        return $this->handleAttendeeAssistanceCreation($event, $member);
    }

    public function createAttendeeFromQR(Event $event, string $id_number)
    {
        $member = Member::whereHas("id_requests", fn ($q) => $q->where("id_number", $id_number))->first();
        return $this->handleAttendeeAssistanceCreation($event, $member);
    }

    private function handleAttendeeAssistanceCreation(Event $event, Member $member)
    {
        $response_status = 0;
        $message = "";
        try {
            DB::beginTransaction();
            
            if(!$member) {
                throw new Exception("Member not found");
            }
            
            $attendee = Attendee::firstOrCreate([
                "first_name" => $member->first_name,
                "middle_name" => $member->middle_name,
                "last_name" => $member->last_name,
                "suffix" => $member->suffix,
                "birth_date" => $member->birth_date,
                "event_id" => $event->id,
            ], [
                "brgy" => $member->brgy,
                "address" => $member->address,
                "gender" => $member->gender,
                "precinct" => $member->precinct,
                "alliance" => $member->alliance,
                "affiliation" => $member->affiliation,
                "civil_status" => $member->civil_status,
                "beneficiary" => $member->beneficiary,
                "religion" => $member->religion,
                "contact_number" => $member->contact_number,
                "is_voter" => $member->is_voter ? 1 : 0,
            ]);
            
            $response = $this->socialServiceAssistanceFacade::createFromAttendee($event, $attendee);
            if($response["status"] !== 1) {
                throw new Exception($response["message"]);
            }

            DB::commit();

            $response_status = 1;

        } catch (Exception $e) {
            $message = $e->getMessage();
            report($e);
            Log::error("Create-Attendee-From-Member-Error " . $e->getMessage());
            DB::rollBack();
        }
        return ["status" => $response_status, "message" => $message];
    }

    public function assistanceBeneficiaries(Request $request, Event $event)
	{
        $social_services = $this->socialServiceAssistanceFacade::getAll($request, $event->id);
        return response()->json([
            'data'=> $social_services["data"],
            'recordsTotal' => $social_services["total"],
            'recordsFiltered' => $social_services["total"],
            'start' => $request->start,
            'length' => $request->length,
            'draw' => $request->draw,
        ]);
	}

    public function scanBeneficiaryId(Request $request, Event $event)
    {
        $id_number = $request->id;
        // Validate if $request->id is a valid encrypted string by length
        if (is_string($request->id) && strlen($request->id) >= 60) {
            $decryptedId = Crypt::decryptString($request->id);
            $id_number = $decryptedId;
        }
        $member = Member::whereHas("id_requests", fn($q) => $q->where("id_number", $id_number))->first();
        if(!$member) {
            return response()->json(['status' => 0, 'message' => 'Member not found'], 404);
        }
        
        $social_service_assistance = SocialServiceAssistance::where('event_id', $event->id)
            ->where('first_name', $member->first_name)
            ->where('last_name', $member->last_name)
            ->where('middle_name', $member->middle_name)
            ->where('suffix', $member->suffix)
            ->where('birth_date', $member->birth_date)
            ->first();
        
        if(!$social_service_assistance) {
            return response()->json(['status' => 0, 'message' => 'Assistance not found for this member'], 404);
        }
        if($social_service_assistance->status === SocialServiceAssistance::STATUS_RELEASED) {
            return response()->json(['status' => 0, 'message' => 'Assistance already released'], 404);
        }
        return response()->json($social_service_assistance);
    }

    public function releaseAssistanceFromIdScan(Request $request, SocialServiceAssistance $social_service_assistance)
    {
        $social_service_assistance->amount = $request->amount;
        $social_service_assistance->received_by = $request->received_by;
        $social_service_assistance->status = SocialServiceAssistance::STATUS_RELEASED;
        $social_service_assistance->received_date = $request->received_date;
        $social_service_assistance->release_date = now();
        $social_service_assistance->releaser_id = auth()->user()->id;
        $social_service_assistance->remarks = $request->remarks;
        $social_service_assistance->approved_date = now();
        $social_service_assistance->approved_by = auth()->user()->id;
        $social_service_assistance->save();

        
        activity("Update Social Service Request via Event QR Scan")
            ->causedBy(auth()->user())
            ->performedOn($social_service_assistance)
            ->withProperties($social_service_assistance)
            ->log('Releasing Social Service Request # ' . $social_service_assistance->control_number);

        Log::info("Releasing Assistance Request Control Number #{$social_service_assistance->control_number} via event QR scan");
        return response()->json(['status' => 1, 'message' => 'Assistance released successfully']);
    }

    public function getReleasedPercentage(Request $request, $event_id)
    {
        $total = SocialServiceAssistance::where('event_id', $event_id)->whereNotIn("status", [SocialServiceAssistance::STATUS_FOR_VALIDATION, SocialServiceAssistance::STATUS_FOR_DELETE])->count();
        $released = SocialServiceAssistance::where('event_id', $event_id)
            ->where('status', SocialServiceAssistance::STATUS_RELEASED)
            ->count();

        $percentage = $total > 0 ? ($released / $total) * 100 : 0;

        return response()->json([
            'percentage' => round($percentage, 2),
            'total' => $total,
            'released' => $released,
        ]);
    }
}