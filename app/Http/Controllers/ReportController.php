<?php

namespace App\Http\Controllers;

use App\Services\SocialServiceAssistance\SocialServiceAssistanceFacade;
use App\Services\Tag\TagFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
	private $socialServiceAssistanceFacade;
	private $tagFacade;

	public function __construct(TagFacade $tagFacade, SocialServiceAssistanceFacade $socialServiceAssistanceFacade)
	{
		$this->tagFacade = $tagFacade;
		$this->socialServiceAssistanceFacade = $socialServiceAssistanceFacade;
	}
	
	public function socialServicesOverview(Request $request)
	{
		$brgys = $this->socialServiceAssistanceFacade::getReportByBrgy($request);
		$request_types = $this->socialServiceAssistanceFacade::getReportByRequestType($request); 
		$statuses = $this->socialServiceAssistanceFacade::getReportByStatus($request); 
		$report_data = $this->socialServiceAssistanceFacade::getReportData($request);
        return view('backend.report.social_services', compact('brgys', 'request_types', 'statuses', 'report_data'));
	}

	public function socialServicesBeneficiaries(Request $request)
	{
		if($request->ajax()){
            $social_services = $this->socialServiceAssistanceFacade::getReportAll($request);
            return response()->json([
                'data'=> $social_services["data"],
                'recordsTotal' => $social_services["total"],
                'recordsFiltered' => $social_services["total"],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{
			$brgys = $this->tagFacade::getTags('brgy');
			$alliances = $this->tagFacade::getTags('alliance');
			$affiliations = $this->tagFacade::getTags('affiliation');
			$religions = $this->tagFacade::getTags('religion');
			$purposes = $this->tagFacade::get('purpose', 0); // get all parent purposes
			$civil_statuses = $this->tagFacade::getTags('civil_status');
			return view('backend.report.social_service_beneficiaries', compact('brgys','alliances','affiliations','religions','purposes','civil_statuses'));
        }
		
	}

	public function exportSocialServicesBeneficiaries(Request $request)
	{
		return $this->socialServiceAssistanceFacade::getReportAll($request, true);
	}

}
