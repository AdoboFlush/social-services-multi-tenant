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
use App\Voter;
use App\WelcomeMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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
        if (Auth::user()->user_type == User::ADMIN) {
            $tenant = $request->attributes->get('tenant_details');

            if ($tenant && isset($tenant['role'])) {
                $current_tenant_context = session('current_tenant_context');
                $current_tenant_id = $current_tenant_context['tenant_id'] ?? null;

                if ($tenant['role'] == User::T_USER_ROLE_LANDLORD && $current_tenant_id == $tenant['tenant_id']) {
                    $viewData = $this->getLandlordViewData($request);
                    return view('backend.dashboard-landlord', $viewData);
                }
            }

            // Admin (non-landlord) dashboard
            $source = $request->has('source') ? $request->source : '';
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
            $current_released_amount = SocialServiceAssistanceFacade::getSocialServicesAmountPerYear(Carbon::now()->format('Y'), $source);
            $last_released_amount = SocialServiceAssistanceFacade::getSocialServicesAmountPerYear(Carbon::now()->subYear(1)->format('Y'), $source);

            $request_type_data = [
                'request_types' => [],
                'count' => [],
            ];
            $request_types = SocialServiceAssistance::with(['tag'])
                ->selectRaw('count(*) as count, sum(amount) as total_amount, request_type_id')
                ->whereYear('created_at', Carbon::now()->format('Y'))
                ->when(!empty($source), fn($q) => $q->where('source', $source))
                ->where('status', SocialServiceAssistance::STATUS_RELEASED)
                ->groupBy(['request_type_id'])
                ->orderBy('request_type_id', 'asc')
                ->get();

            foreach ($request_types as $request_type) {
                if (!empty($request_type->tag->name)) {
                    $request_type_data['request_types'][] = $request_type->tag->name;
                    $request_type_data['count'][] = $request_type->count;
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

            return view('backend.dashboard-admin', compact(['data']));

        } elseif (Auth::user()->user_type == User::TAGGER) {
            return redirect(route('voter.tagging.view', [], false));
        } else {
            return view('backend.user_panel.dashboard-user', []);
        }
    }

    /**
     * Prepare view data for landlord dashboard by aggregating across tenant children.
     *
     * @param Request $request
     * @return array
     */
    private function getLandlordViewData(Request $request): array
    {
        $tenants_data = [];
        $assistances_by_type = [];
        $tenant_children = $request->attributes->get('tenant_children') ?? [];

        foreach ($tenant_children as $child_tenant) {
            $tenant_context = [
                'database' => $child_tenant['database'],
                'username' => $child_tenant['username'],
                'password' => $child_tenant['password'],
            ];

            $child_tenant['pending_assistance_count'] = SocialServiceAssistance::onTenantContext($tenant_context)
                ->where('status', SocialServiceAssistance::STATUS_PENDING)
                ->count();

            $child_tenant['released_assistance_count'] = SocialServiceAssistance::onTenantContext($tenant_context)
                ->where('status', SocialServiceAssistance::STATUS_RELEASED)
                ->count();

            $child_tenant['voter_count'] = Voter::onTenantContext($tenant_context)->count();

            // Fetch assistance data by request type for this tenant
            $assistance_records = SocialServiceAssistance::onTenantContext($tenant_context)
                ->with(['tag:id,name'])
                ->selectRaw('count(*) as count, sum(amount) as total_amount, request_type_id')
                ->groupBy('request_type_id')
                ->get();

            // Aggregate data across all tenant children
            foreach ($assistance_records as $record) {
                $request_type_id = $record->request_type_id;
                $request_type_name = $record->tag ? $record->tag->name : 'Unknown';

                if (!isset($assistances_by_type[$request_type_id])) {
                    $assistances_by_type[$request_type_id] = [
                        'name' => $request_type_name,
                        'count' => 0,
                        'amount' => 0,
                    ];
                }

                $assistances_by_type[$request_type_id]['count'] += $record->count;
                $assistances_by_type[$request_type_id]['amount'] += $record->total_amount;
            }

            $tenants_data[] = $child_tenant;
        }

        // Format data for Chart.js
        $assistances_data_arr = [
            'request_types' => [],
            'count' => [],
            'amount' => [],
        ];

        foreach ($assistances_by_type as $type_id => $data) {
            $assistances_data_arr['request_types'][] = $data['name'];
            $assistances_data_arr['count'][] = $data['count'];
            $assistances_data_arr['amount'][] = number_format($data['amount'], 2);
        }

        // Fetch monthly assistance data for all tenant children
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthly_current_year = array_fill(0, 12, 0);
        $monthly_last_year = array_fill(0, 12, 0);

        foreach ($tenant_children as $child_tenant) {
            $tenant_context = [
                'database' => $child_tenant['database'],
                'username' => $child_tenant['username'],
                'password' => $child_tenant['password'],
            ];

            // Current year monthly data
            $current_year_data = SocialServiceAssistance::onTenantContext($tenant_context)
                ->selectRaw('MONTH(release_date) as release_month, SUM(amount) as total_amount')
                ->whereYear('release_date', Carbon::now()->format('Y'))
                ->groupBy('release_month')
                ->get();

            foreach ($current_year_data as $data) {
                $month_index = intval($data->release_month) - 1;
                if ($month_index >= 0 && $month_index < 12) {
                    $monthly_current_year[$month_index] += $data->total_amount ?? 0;
                }
            }

            // Last year monthly data
            $last_year_data = SocialServiceAssistance::onTenantContext($tenant_context)
                ->selectRaw('MONTH(release_date) as release_month, SUM(amount) as total_amount')
                ->whereYear('release_date', Carbon::now()->subYear(1)->format('Y'))
                ->groupBy('release_month')
                ->get();

            foreach ($last_year_data as $data) {
                $month_index = intval($data->release_month) - 1;
                if ($month_index >= 0 && $month_index < 12) {
                    $monthly_last_year[$month_index] += $data->total_amount ?? 0;
                }
            }
        }

        $monthly_data = [
            'months' => $months,
            'current_year' => $monthly_current_year,
            'last_year' => $monthly_last_year,
        ];

        // Total for current year (sum of monthly amounts)
        $current_year_total = array_sum($monthly_current_year);
        $current_year_total_formatted = number_format($current_year_total, 2);

        return compact('tenants_data', 'assistances_data_arr', 'monthly_data', 'current_year_total', 'current_year_total_formatted');
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
