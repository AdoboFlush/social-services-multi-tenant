<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberCodeRequest;
use App\Http\Requests\MemberLoginRequest;
use App\Http\Requests\MemberValidateRequest;
use App\Member;
use App\Services\Event\EventFacade;
use App\Services\IdRequest\IdRequestFacade;
use App\Services\MemberCode\MemberCodeFacade;
use App\Services\SocialServiceAssistance\SocialServiceAssistanceFacade;
use App\Services\Template\TemplateFacade;
use App\Services\User\UserFacade;
use App\Template;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    protected $memberCodeFacade;
    protected $idRequestFacade;
    protected $templateFacade;
    protected $userFacade;
    protected $eventFacade;
    protected $socialServiceAssistanceFacade;

    public function __construct(
        MemberCodeFacade $memberCodeFacade,
        IdRequestFacade $idRequestFacade,
        TemplateFacade $templateFacade,
        UserFacade $userFacade,
        EventFacade $eventFacade,
        SocialServiceAssistanceFacade $socialServiceAssistanceFacade
    ) {
        $this->memberCodeFacade = $memberCodeFacade;
        $this->idRequestFacade = $idRequestFacade;
        $this->templateFacade = $templateFacade;
        $this->userFacade = $userFacade;
        $this->eventFacade = $eventFacade;
        $this->socialServiceAssistanceFacade = $socialServiceAssistanceFacade;
    }

    public function index()
    {
        if(Auth::check()) {
            return redirect(route("guest.profile"));
        } else {
            return view("guest.landing");
        }
    }

    public function register(Request $request)
    {
        $code = isset($request->all()['code']) ? $request->all()['code'] : null;

        if (!isset($code)) {
            return redirect(route("guest.landing", [], false));
        }

        if ($this->memberCodeFacade::isMemberCodeActive($code)) {
            return redirect(route("guest.login", [], false))->withSuccess(_lang("Member code is already active. You can now login."));
        }

        $code_with_member = $this->memberCodeFacade::getMemberByCode($code);
        $member = $code_with_member->member;
        $account_number = $member->account_number;

        return view("guest.registration", compact('account_number'));
    }

    public function login()
    {
        return view("guest.login");
    }

    public function checkMemberCode(MemberCodeRequest $request)
    {
        $code = $request->member_code;

        if ($code && $this->memberCodeFacade::isMemberCodeActive($code)) {
            return redirect(route("guest.login", [], false))->withSuccess(_lang("Member code is already active. You can now login."));
        } else {
            return redirect("register?code={$code}")->withSuccess(_lang("Member code exist. You can now register."));
        }
    }

    public function validateRegistration(MemberValidateRequest $request)
    {

        if ($this->userFacade::createMemberUser($request)) {
            return redirect(route("guest.login", [], false))->withSuccess(_lang("Successful registration."));
        }

        return back()->with('error', _lang("You cannot register at this time. Please report to AA staff."));
    }

    public function edit(Request $request)
    {
        if (auth()->user()) {
            $account_number = auth()->user()->account_number;

            $member = Member::where("account_number", $account_number)->with('id_requests')->first();

            $template = Template::where("allowed_user_create", true)->first();
			
			$id_request = null;
            if ($member && $member->id_requests && count($member->id_requests) > 0) {
                $id_request =  $member->id_requests()->whereHas("template", fn ($q) => $q->where("allowed_user_create", true))->first();
            }
			$compact = compact('member', 'id_request', 'template');
            return view("guest.edit", $compact);
        } else {
            return redirect(route('guest.login', [], false));
        }
    }

    public function create(Request $request)
    {
        if (auth()->user()) {
            $account_number = auth()->user()->account_number;
            $member = Member::where("account_number", $account_number)->with('id_requests')->first();
			if ($member && $member->id_requests && count($member->id_requests) > 0) {
				return redirect('profile')->with('error', 'You already have an ID.');
			}
            $template = Template::where("allowed_user_create", true)->first();
			$compact = compact('member', 'template');
            return view("guest.create", $compact);
        } else {
            return redirect(route('guest.login', [], false));
        }
    }

    public function storeProfile(Request $request)
    {
        if(empty($request->profile_pic)) {
            return back()->with('error', 'Profile picture is required.');
        }

        if(empty($request->signature)) {
            return back()->with('error', 'Signature is required.');
        }

        return $this->idRequestFacade::store($request, back()->with('success', 'ID creation successful'));
    }

    public function updateProfile(Request $request)
    {
        if(!$request->has("profile_pic")) {
            return back()->with('error', 'Profile picture is required.');
        }

        if(!$request->has("signature")) {
            return back()->with('error', 'Signature is required.');
        }
        
        return $this->idRequestFacade::update($request, back()->with('success', 'ID update successful'));
    }

    public function profile(Request $request)
    {
        if (auth()->user()) {
            $account_number = auth()->user()->account_number;

            $member = Member::where("account_number", $account_number)->with('id_requests')->first();

            $compact = compact('member');

            $template = Template::where("allowed_user_create", true)->first();

            $compact = compact('member', 'template');

            if ($member) {
                $event = $this->eventFacade::getMemberEvents([
                    'first_name' => trim($member->first_name),
                    'last_name' => trim($member->last_name),
                    'middle_name' => trim($member->middle_name),
                    'suffix' => trim($member->suffix),
                    'gender' => trim($member->gender),
                    'birth_date' => trim($member->birth_date),
                ]);

                $event_count = [
                    "past" => count($event["past"]),
                    "ongoing" => count($event["ongoing"]),
                    "upcoming" => count($event["upcoming"]),
                ];

                $compact = compact('member', 'template', 'event_count');
            }

            if ($member && $member->id_requests && count($member->id_requests) > 0) {
                $id_request =  $member->id_requests()->whereHas("template", fn ($q) => $q->where("allowed_user_create", true))->first();
        
                $compact = compact('member', 'template', 'event_count', 'id_request',);
            }

            return view("guest.profile", $compact);
        } else {
            return redirect(route('guest.login', [], false));
        }
    }

    public function previewID(Request $request)
    {
        if (auth()->user()) {
            $data = $this->idRequestFacade::getById($request);
            $template = $this->templateFacade::getById($data->template_id);
            $template = json_decode($template->properties_json, true);
            $front = $template['front'];
            $back = $template['back'];
            $front_bg = $template['front']['bg'];
            $back_bg = $template['back']['bg'];
            return view('guest.id', compact('data', 'front', 'back', 'front_bg', 'back_bg'));
        } else {
            return redirect(route('guest.login', [], false));
        }
    }

    public function assistance()
    {
        if (auth()->user()) {
            $account_number = auth()->user()->account_number;
            $social_service_assistance = null;
    
            $member = Member::where("account_number", $account_number)->first();
    
            if ($member && $member->last_name && $member->first_name && $member->birth_date){
                $social_service_assistance = $this->socialServiceAssistanceFacade::getMemberAssistanceHistory([
                    'first_name' => trim($member->first_name),
                    'last_name' => trim($member->last_name),
                    'middle_name' => trim($member->middle_name),
                    'suffix' => trim($member->suffix),
                    'birth_date' => trim($member->birth_date),
                ]);  
            }

            $compact = compact('social_service_assistance');

            return view("guest.assistance", $compact);
        } else {
            return redirect(route('guest.login', [], false));
        }
    }

    public function event()
    {
        if (auth()->user()) {
            $account_number = auth()->user()->account_number;
            $event = null;
    
            $member = Member::where("account_number", $account_number)->first();
    
            if ($member && $member->last_name && $member->first_name && $member->birth_date){
                $event = $this->eventFacade::getMemberEvents([
                    'first_name' => trim($member->first_name),
                    'last_name' => trim($member->last_name),
                    'middle_name' => trim($member->middle_name),
                    'suffix' => trim($member->suffix),
                    'gender' => trim($member->gender),
                    'birth_date' => trim($member->birth_date),
                ]);  
            }

            $compact = compact('event');

            return view("guest.event", $compact);
        } else {
            return redirect(route('guest.login', [], false));
        }
    }
}
