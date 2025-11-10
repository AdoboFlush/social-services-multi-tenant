<?php

namespace App\Http\Controllers;
use App\Services\Tag\TagFacade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Services\IdRequest\IdRequestFacade;
use App\Services\Member\MemberFacade;
use App\Services\Template\TemplateFacade;
use App\IdRequest;
use App\SocialServiceAssistance;
use App\Voter;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class IdRequestController extends Controller
{

    protected $idRequestFacade;
    protected $memberFacade;
    protected $templateFacade;
    protected $tagFacade;

	public function __construct(IdRequestFacade $idRequestFacade, MemberFacade $memberFacade, TemplateFacade $templateFacade, TagFacade $tagFacade)
	{
		$this->idRequestFacade = $idRequestFacade;
        $this->memberFacade = $memberFacade;
        $this->templateFacade = $templateFacade;
        $this->tagFacade = $tagFacade;
	}

    public function index(Request $request)
    { 
        if($request->ajax()){
            $id_request = $this->idRequestFacade::getAll($request);
            return response()->json([
                'data'=> $id_request["data"],
                'recordsTotal' => $id_request["total"],
                'recordsFiltered' => $id_request["total"],
                'start' => $request->start, 
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{
            $templates = $this->templateFacade::get();
            $alliances = $this->tagFacade::get('alliance', 0);
            $affiliations = $this->tagFacade::get('affiliation', 0);
    
            return view('backend.id_system.request.list', compact('templates', 'alliances', 'affiliations'));
        }
    }
	
	public function export(Request $request)
    {
		 return $this->idRequestFacade::getAll($request, true);
	}
	
	public function downloadMultiple(Request $request)
	{
		$model = $this->idRequestFacade::getAll($request, false, true);
		return $model->get();
	}

    public function create(Request $request)
    {
        $templates = $this->templateFacade::get();
        return view('backend.id_system.request.create', compact('templates'));
    }

    public function createFromMember(Request $request)
    {
        $selected_id = $request->id;
        $templates = $this->templateFacade::get();
        $selected_member = $this->memberFacade::getById($request);
        return view('backend.id_system.request.create_from_member', compact('templates', 'selected_id', 'selected_member'));
    }

    public function store(Request $request)
    {
        return $this->idRequestFacade::store($request);
    }

    public function edit(Request $request)
    {
        $data = $this->idRequestFacade::getById($request);
        $templates = $this->templateFacade::get();
        return view('backend.id_system.request.edit', compact('data', 'templates'));
    }

    public function update(Request $request)
    {
        return $this->idRequestFacade::update($request);
    }

    public function preview(Request $request)
    {
        $data = $this->idRequestFacade::getById($request);
        $template = $this->templateFacade::getById($data->template_id);
        $template = json_decode($template->properties_json, true);
        $front = $template['front'];
        $back = $template['back'];
        $front_bg = $template['front']['bg'];
        $back_bg = $template['back']['bg'];

        return view('backend.id_system.request.modal.preview', compact('data', 'front', 'back', 'front_bg', 'back_bg'));
    }
	
	public function multiplePreview(Request $request)
	{
		$id_requests = [];
		if($request->has("selected_ids")){
			$selected_ids = explode(",", $request->selected_ids);
			foreach($selected_ids as $selected_id){
                $id_request = IdRequest::with("member")->find($selected_id);
				$template = $this->templateFacade::getById($id_request->template_id);
				$template = json_decode($template->properties_json, true);
				$front = @$template['front'];
				$back = @$template['back'];
				$front_bg = @$template['front']['bg'];
				$back_bg = @$template['back']['bg'];
				$id_requests[] = [
					'front' => $front,
					'back' => $back,
					'front_bg' => $front_bg,
					'back_bg' => $back_bg,
					'data' => $id_request,
				];
            }
		}
		return view('backend.id_system.request.modal.multiple_preview', compact('id_requests'));
	}

    private $chunked_id_requests = [];
    private $chunk_index = 1;

    public function multipleDownload(Request $request)
	{
		if($request->has("selected_ids")){
			$selected_ids = explode(",", $request->selected_ids);
            IdRequest::with("member")
                ->whereIn("id", $selected_ids)
                ->chunk(10, function ($id_requests) {
                    $end_index = count($id_requests) + $this->chunk_index;
                    foreach($id_requests as $id_request) {
                        $template = $this->templateFacade::getById($id_request->template_id);
                        $template = json_decode($template->properties_json, true);
                        $front = @$template['front'];
                        $back = @$template['back'];
                        $front_bg = @$template['front']['bg'];
                        $back_bg = @$template['back']['bg'];
                        $this->chunked_id_requests["{$this->chunk_index}-{$end_index}"][] = [
                            'front' => $front,
                            'back' => $back,
                            'front_bg' => $front_bg,
                            'back_bg' => $back_bg,
                            'data' => $id_request,
                        ];
                        IdRequestFacade::updateDownloadStats($id_request);
                    }
                    $this->chunk_index += count($id_requests);
                });

            $chunked_id_requests = $this->chunked_id_requests;
		}
		return view('backend.id_system.request.modal.download_multiple', compact('chunked_id_requests'));
    }

    public function getIdsPerPage(Request $request)
    {
        $id_requests = [];
        $id_requests_raw = $this->idRequestFacade::getAll($request, false, true);
        $id_requests_raw = $id_requests_raw->paginate(10);
        $last_page = $id_requests_raw->lastPage();

        if($request->has("part") && $request->part == 1) {
            $part = "back";
        } else {
            $part = "front";
        }

        foreach($id_requests_raw as $id_request) {
            $template = $this->templateFacade::getById($id_request->template_id);
            $template = json_decode($template->properties_json, true);
            $front = @$template['front'];
            $back = @$template['back'];
            $front_bg = @$template['front']['bg'];
            $back_bg = @$template['back']['bg'];
            $id_requests[] = [
                'front' => $front,
                'back' => $back,
                'front_bg' => $front_bg,
                'back_bg' => $back_bg,
                'data' => $id_request,
            ];
            IdRequestFacade::updateDownloadStats($id_request);
        }
        $file_name = "ids-page-$part-{$request->page}";
        return view('backend.id_system.request.template.id_per_page', compact('id_requests', 'file_name', 'last_page', 'part'));
    }

    public function generateMultipleIds(Request $request)
    {
        $selected_ids = explode(",", $request->selected_ids);
        $id_requests_raw = IdRequest::whereIn("id", $selected_ids)->get();
        $last_page = $request->last_page;
        if($request->has("part") && $request->part == 1) {
            $part = "back";
        } else if($request->has("part") && $request->part == 0) {
            $part = "front";
        } else {
            $part = "all";
        }

        $id_requests = [];

        foreach($id_requests_raw as $id_request) {
            $template = $this->templateFacade::getById($id_request->template_id);
            $template = json_decode($template->properties_json, true);
            $front = @$template['front'];
            $back = @$template['back'];
            $front_bg = @$template['front']['bg'];
            $back_bg = @$template['back']['bg'];
            $id_requests[] = [
                'front' => $front,
                'back' => $back,
                'front_bg' => $front_bg,
                'back_bg' => $back_bg,
                'data' => $id_request,
            ];
            IdRequestFacade::updateDownloadStats($id_request);
        }

        $file_name = "ids-batch-{$part}-{$request->batch_number}";
        return view($part == "all" ? 'backend.id_system.request.template.id_per_page_all_sides' : 'backend.id_system.request.template.id_per_page', compact('id_requests', 'file_name', 'last_page', 'part'));

    }

    public function showMemberId(Request $request)
    {
        $data = $this->idRequestFacade::getByIdNumber($request);
        $template = $this->templateFacade::getById($data->template_id);
        $template = json_decode($template->properties_json, true);
        $front = $template['front'];
        $back = $template['back'];
        $front_bg = $template['front']['bg'];
        $back_bg = $template['back']['bg'];
        return view('backend.id_system.request.modal.id', compact('data', 'front', 'back', 'front_bg', 'back_bg'));
    }

    public function scanResult(Request $request)
    {
        try {
            // Validate if $request->id is a valid encrypted string by length
            if (is_string($request->id) && strlen($request->id) >= 60) {
                $decryptedId = Crypt::decryptString($request->id);
                $request->id = $decryptedId;
            }
            $data = $this->idRequestFacade::getByIdNumber($request);
            if($data){
                $member = $data->member->toArray();
                $voter = Voter::where("first_name", $member['first_name'])
                    ->where("last_name", $member['last_name'])
                    ->where("middle_name", $member['middle_name'])
                    ->where("suffix", $member['suffix'])
                    ->where("birth_date", $member['birth_date'])
                    ->first();

                $voter = $voter ? $voter->toArray() : [];

                $social_services = SocialServiceAssistance::where("first_name", $member['first_name'])
                    ->where("last_name", $member['last_name'])
                    ->where("middle_name", $member['middle_name'])
                    ->where("suffix", $member['suffix'])
                    ->where("birth_date", $member['birth_date'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                $data = $data->toArray();
                return view('backend.id_system.request.scan_result', compact('data', 'member', 'voter', 'social_services'));
            }else{
                return redirect('id_system/requests')->withError('Scanned ID is invalid. Please try again.');
            }

        } catch (Exception $e) {
            Log::info($e->getMessage());
            return back()->withError('An error occurred. Please check the input.');
        }
        return back();
    }

    public function delete(Request $request)
    {
        return response($this->idRequestFacade::deleteMultiple($request));
    }

    public function convertHTMLToPDF()
    {
        return $this->idRequestFacade::convertHTMLToPDF();
    }

    public function updateDownloadStats(IdRequest $id_request)
    {
        $status = IdRequestFacade::updateDownloadStats($id_request);
        return [
            "status" => $status
        ];
    }

}
