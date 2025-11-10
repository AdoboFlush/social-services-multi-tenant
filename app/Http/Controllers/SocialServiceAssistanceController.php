<?php

namespace App\Http\Controllers;

use App\Event;
use App\Member;
use App\Services\Member\MemberFacade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Services\SocialServiceAssistance\SocialServiceAssistanceFacade;
use App\Services\Tag\TagFacade;
use App\Services\Voter\VoterFacade;
use App\SocialServiceAssistance;
use App\Tag;
use App\User;
use Illuminate\Support\Facades\Crypt;

class SocialServiceAssistanceController extends Controller
{

    private $socialServiceAssistanceFacade;
    private $tagFacade;
    private $voterFacade;
    private $memberFacade;

    public function __construct(
        SocialServiceAssistanceFacade $socialServiceAssistanceFacade,
        TagFacade $tagFacade,
        VoterFacade $voterFacade,
        MemberFacade $memberFacade
    ) {
        $this->socialServiceAssistanceFacade = $socialServiceAssistanceFacade;
        $this->tagFacade = $tagFacade;
        $this->voterFacade = $voterFacade;
        $this->memberFacade = $memberFacade;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $social_services = $this->socialServiceAssistanceFacade::getAll($request);
            return response()->json([
                'data' => $social_services["data"],
                'recordsTotal' => $social_services["total"],
                'recordsFiltered' => $social_services["total"],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        } else {
            $users = User::get();
            $request_types = Tag::where('type', 'purpose')->where('parent_id', '0')->get();
            return view('backend.social_service.list', compact('users', 'request_types'));
        }
    }

    public function create(Request $request)
    {
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::get('purpose', 0); // get all parent purposes
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        return view('backend.social_service.create', compact('brgys', 'alliances', 'affiliations', 'religions', 'purposes', 'civil_statuses'));
    }

    public function createWithVoter(Request $request)
    {
        $voter = $this->voterFacade::getById($request);
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::get('purpose', 0); // get all parent purposes
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $event_id = $request->input('event_id', null);
        $event = $event_id ? Event::find($event_id) : null;
        return view('backend.social_service.create_with_voter', compact('voter', 'brgys', 'alliances', 'affiliations', 'religions', 'purposes', 'civil_statuses', 'event'));
    }

    public function createWithMember(Request $request)
    {
        $member = $this->memberFacade::getById($request);
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::get('purpose', 0); // get all parent purposes
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        return view('backend.social_service.create_with_member', compact('member', 'brgys', 'alliances', 'affiliations', 'religions', 'purposes', 'civil_statuses'));
    }

    public function store(Request $request)
    {
        return $this->socialServiceAssistanceFacade::store($request);
    }

    public function edit(Request $request)
    {
        $socialService = $this->socialServiceAssistanceFacade::getById($request);
        $brgys = $this->tagFacade::getTags('brgy');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::get('purpose', 0); // get all parent purposes
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $event_id = $socialService->event_id ?? null;
        $event_name = null;
        if ($event_id) {
            $event = Event::find($event_id);
            $event_name = $event ? $event->name : null;
        }
        return view('backend.social_service.edit', compact('socialService', 'brgys', 'alliances', 'affiliations', 'religions', 'purposes', 'civil_statuses', 'event_id', 'event_name'));
    }

    public function show(Request $request)
    {
        $socialService = $this->socialServiceAssistanceFacade::getById($request);
        $socialService = $socialService->toArray();
        return view('backend.social_service.modal.show', compact('socialService'));
    }

    public function release(Request $request)
    {
        $socialService = $this->socialServiceAssistanceFacade::getById($request);
        $socialService = $socialService->toArray();
        return view('backend.social_service.modal.release', compact('socialService'));
    }

    public function update(Request $request)
    {
        return $this->socialServiceAssistanceFacade::update($request);
    }

    public function updateStatusMultiple(Request $request)
    {
        return response($this->socialServiceAssistanceFacade::updateStatusMultiple($request));
    }

    public function delete(Request $request)
    {
        return response($this->socialServiceAssistanceFacade::deleteMultiple($request));
    }

    public function fetch()
    {
        $this->socialServiceAssistanceFacade::fetch();
    }

    public function getCurrentControlNumber(Request $request)
    {
        return $this->socialServiceAssistanceFacade::getCurrentControlNumber($request);
    }

    public function getEventsByRequestTypeId(Request $request, int $request_type_id)
    {
        $query = $request->input('q', '');
        $events = Event::where('request_type_id', $request_type_id)
            ->when($query, function($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                ];
            });
        return response()->json($events);
    }

    public function scanIdForRelease(Request $request)
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
        
        $social_service_assistances = SocialServiceAssistance::where('status', SocialServiceAssistance::STATUS_APPROVED)
            ->where('first_name', $member->first_name)
            ->where('last_name', $member->last_name)
            ->where('middle_name', $member->middle_name)
            ->where('suffix', $member->suffix)
            ->where('birth_date', $member->birth_date)
            ->get();
        
        if(count($social_service_assistances) <= 0) {
            return response()->json(['status' => 0, 'message' => 'No "Approved" Assistances found for this member'], 404);
        }

        return response()->json(['status' => 1, 'message' => 'success', 'assistances' => $social_service_assistances]);
    }
}
