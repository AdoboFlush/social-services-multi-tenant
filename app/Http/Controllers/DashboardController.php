<?php

namespace App\Http\Controllers;

use App\Repositories\WelcomeMessage\WelcomeMessageInterface;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLog\ActivityLogFacade;
use App\Services\SocialServiceAssistance\SocialServiceAssistanceFacade;
use App\Services\Voter\VoterFacade;
use App\SocialServiceAssistance;
use App\User;
use App\WelcomeMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    private $userService;
    private $activityLogFacade;
    private $welcomeMessageInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService, 
                                ActivityLogFacade $activityLogFacade,
                                WelcomeMessageInterface $welcomeMessageInterface
                                )
    {
        $this->userService = $userService;
        $this->activityLogFacade = $activityLogFacade;
        $this->welcomeMessageInterface = $welcomeMessageInterface;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ( Auth::user()->user_type == User::ADMIN){

            $source = $request->has("source") ? $request->source : "";
            $brgy = VoterFacade::getVoterDemographics('brgy');
            $alliance = VoterFacade::getVoterDemographics('alliance');
            $affiliation = VoterFacade::getVoterDemographics('affiliation');
            $civil_status = VoterFacade::getVoterDemographics('civil_status');
            $religion = VoterFacade::getVoterDemographics('religion');
            $sectoral = VoterFacade::getVoterDemographics('sectoral');
            $position = VoterFacade::getVoterDemographics('position');
            $voter_count = VoterFacade::getTotalCount($request);
            $pending_social_service_count = SocialServiceAssistanceFacade::getTotalCountByStatus('Pending');
            $approved_social_service_count = SocialServiceAssistanceFacade::getTotalCountByStatus('Approved');
            $released_social_service_count = SocialServiceAssistanceFacade::getTotalCountByStatus('Released');
            $welcome_message = $this->welcomeMessageInterface->getCurrentMessage();
            $current_released_amount = SocialServiceAssistanceFacade::getSocialServicesAmountPerYear(Carbon::now()->format("Y"), $source);
            $last_released_amount = SocialServiceAssistanceFacade::getSocialServicesAmountPerYear(Carbon::now()->subYear(1)->format("Y"), $source);

            $request_type_data = [
                "request_types" => [],
                "count" => [],
            ];
            $request_types = SocialServiceAssistance::with(['tag'])
                ->selectRaw('count(*) as count, sum(amount) as total_amount, request_type_id')
                ->whereYear('created_at', Carbon::now()->format("Y"))
                ->when(!empty($source), fn($q) => $q->where("source", $source))
                ->where("status", SocialServiceAssistance::STATUS_RELEASED)
                ->groupBy(['request_type_id'])
                ->orderBy('request_type_id', 'asc')
                ->get();
            
            foreach($request_types as $request_type) {
                if(!empty($request_type->tag->name)) {
                    $request_type_data["request_types"][] = $request_type->tag->name;
                    $request_type_data["count"][] = $request_type->count;
                }
            }
            
            $data = [
                'brgy' => $brgy,
                'alliance' => $alliance,
                'affiliation' => $affiliation,
                'civil_status' => $civil_status,
                'religion' => $religion,
                'position' => $position,
                'voter_count' => $voter_count,
                'pending_social_service_count' => $pending_social_service_count,
                'approved_social_service_count' => $approved_social_service_count,
                'released_social_service_count' => $released_social_service_count,
                'welcome_message' => $welcome_message,
                'current_released_amount' => $current_released_amount,
                'last_released_amount' => $last_released_amount,
                'sectoral' => $sectoral,
                'request_type_data' => $request_type_data,
            ];

            return view('backend.dashboard-admin',compact(['data']));

        } elseif ( Auth::user()->user_type == User::TAGGER ){
            return redirect(route('voter.tagging.view', [], false));
        } else {
            return view('backend.user_panel.dashboard-user',[]);
       }
    }

    /**
     * @param Request $request
     *
     * @return json
     */
    public function json_month_wise_deposit(Request $request)
    {
        echo json_encode($this->userService->getUserTotalTransactionPerMonthByYearAndCurrency($request, 'deposit'));
        exit();
    }

    /**
     * @param Request $request
     *
     * @return json
     */
    public function json_month_wise_withdraw(Request $request)
    {
        echo json_encode($this->userService->getUserTotalTransactionPerMonthByYearAndCurrency($request, 'withdrawal'));
        exit();
    }

    public function getAllDashboardUsers(Request $request) {
        return $this->userService->getAllDashboardUsers($request);
    }

    public function getDashboardExport(Request $request) {
        return $this->userService->getDashboardExport($request);
    }

    public function getDashboardExportBalance(Request $request) {
        return $this->userService->getDashboardExportBalance($request);
    }

    public function switchTheme(Request $request) {
        $theme = $request->cookie("use_theme");
        $minutes = 300;
        if($theme == "dark") {
            $new_cookie = cookie('use_theme', 'light', $minutes);
        } else {
            $new_cookie = cookie('use_theme', 'dark', $minutes);
        }
        return back()->withCookie($new_cookie);
    }
}
