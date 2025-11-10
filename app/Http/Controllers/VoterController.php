<?php

namespace App\Http\Controllers;

use App\Member;
use App\Services\Member\MemberFacade;
use App\Services\MemberCode\MemberCodeFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Voter\VoterFacade;
use App\Services\Tag\TagFacade;
use App\Traits\ActivityLog\LogsChanges;
use App\Voter;
use App\VoterTagDetail;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VoterController extends Controller
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

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $voters = $this->voterFacade::getAll($request);
            return response()->json([
                'data' => $voters["data"],
                'recordsTotal' => $voters["count"],
                'recordsFiltered' => $voters["count"],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        } else {
            return view('backend.voter.list');
        }
    }

    public function indexArchived(Request $request)
    {
        if ($request->ajax()) {
            $voters = $this->voterFacade::getAll($request, true);
            return response()->json([
                'data' => $voters["data"],
                'recordsTotal' => $voters["count"],
                'recordsFiltered' => $voters["count"],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        } else {
            return view('backend.voter.archived');
        }
    }

    public function indexSeniorCitizenVoters(Request $request)
    {
        if ($request->ajax()) {
            $voters = $this->voterFacade::getAllSeniorCitizenVoters($request);
            return response()->json([
                'data' => $voters["data"],
                'recordsTotal' => $voters["count"],
                'recordsFiltered' => $voters["count"],
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        } else {
            return view('backend.senior_citizen.list');
        }
    }

    public function generateSeniorCitizenCSV()
    {
        return $this->voterFacade::generateSeniorCitizenCSV();
    }

    public function create(Request $request)
    {
        $brgys = $this->tagFacade::getTags('brgy');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::getTags('purpose');
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $beneficiaries = $this->tagFacade::getTags('beneficiaries');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $sectorals = $this->tagFacade::getTags('sectoral');
        $positions = $this->tagFacade::getTags('position');
        $party_lists = $this->tagFacade::getTags('party_list');

        return view(
            'backend.voter.modal.create',
            compact(
                'brgys',
                'religions',
                'purposes',
                'civil_statuses',
                'beneficiaries',
                'alliances',
                'affiliations',
                'sectorals',
                'positions',
                'party_lists',
            )
        );
    }

    public function import(Request $request)
    {
        return $this->voterFacade::import($request);
    }

    public function importToMembers(Request $request)
    {

        if ($request->isMethod("POST")) {

            if (empty($request->filter_field) || empty($request->filter_search)) {
                return redirect('voters')
                    ->withError("Must filter the records to avoid connection timeout");
            }

            $voters = Voter::when(
                !empty($request->filter_field) && !empty($request->filter_search),
                fn($q) => $q->where($request->filter_field, $request->filter_search)
            )->get();

            $totalCount = $inserted = $dup = $failed = 0;

            set_time_limit(300);

            foreach ($voters as $voter) {
                try {
                    $model = new Member;
                    $model->account_number = $this->memberFacade::generateAccountNumber();
                    $model->first_name = trim($voter->first_name);
                    $model->last_name = trim($voter->last_name);
                    $model->middle_name = trim($voter->middle_name);
                    $model->suffix = trim($voter->suffix);
                    $model->birth_date = trim($voter->birth_date);
                    $model->address = trim($voter->address);
                    $model->gender = trim($voter->gender);
                    $model->precinct = trim($voter->precinct);
                    $model->brgy = trim($voter->brgy);
                    $model->alliance = trim($voter->alliance);
                    $model->affiliation = trim($voter->affiliation);
                    $model->civil_status = trim($voter->civil_status);
                    $model->religion = trim($voter->religion);
                    $model->contact_number = trim($voter->contact_number);
                    $model->remarks = "Imported from Voters";
                    $model->is_voter = 1;
                    $model->parent_id = $voter->id;
                    $model->status = 1;
                    $affected_row = $model->save();

                    // create a member code
                    $this->memberCodeFacade::generateMemberCode($model->id);

                    $inserted++;
                    activity("Create Member from Voters")
                        ->causedBy(Auth::user())
                        ->performedOn($model)
                        ->withProperties($model)
                        ->log('Create Member : ' . $model->account_number);
                } catch (QueryException $e) {
                    $errorCode = $e->errorInfo[1];
                    if ($errorCode == 1062) {
                        $dup++;
                    }
                } catch (Exception $e) {
                    $failed++;
                }
                $totalCount++;
            };

            return redirect('voters')
                ->withSuccess("Done importing of voters to members")
                ->withMessages([
                    "Total Voters: {$totalCount}",
                    "Inserted: {$inserted}",
                    "Already exists in Members: {$dup}",
                    "Failed: {$failed}",
                ]);
        } else {
            return view('backend.voter.modal.import_to_members');
        }
    }

    public function store(Request $request)
    {
        return $this->voterFacade::store($request);
    }

    public function edit(Request $request)
    {
        $voter = $this->voterFacade::getById($request);
        $brgys = $this->tagFacade::getTags('brgy');
        $religions = $this->tagFacade::getTags('religion');
        $purposes = $this->tagFacade::getTags('purpose');
        $civil_statuses = $this->tagFacade::getTags('civil_status');
        $beneficiaries = $this->tagFacade::getTags('beneficiaries');
        $alliances = $this->tagFacade::getTags('alliance');
        $affiliations = $this->tagFacade::getTags('affiliation');
        $sectorals = $this->tagFacade::getTags('sectoral');
        $positions = $this->tagFacade::getTags('position');
        $party_lists = $this->tagFacade::getTags('party_list');
        
        return view(
            'backend.voter.modal.edit',
            compact(
                'voter',
                'brgys',
                'religions',
                'purposes',
                'civil_statuses',
                'beneficiaries',
                'alliances',
                'affiliations',
                'sectorals',
                'positions',
                'party_lists',
            )
        );
    }

    public function show(Request $request)
    {
        $voter = $this->voterFacade::getById($request);
        $voter = $voter->toArray();
        return view('backend.voter.modal.show', compact('voter'));
    }

    public function update(Request $request)
    {
        return $this->voterFacade::update($request);
    }

    public function search(Request $request)
    {
        return response()->json($this->voterFacade::search($request));
    }

    public function get(Request $request)
    {
        return response()->json($this->voterFacade::getById($request));
    }

    public function delete(Request $request)
    {
        return response($this->voterFacade::deleteMultiple($request));
    }

    public function fetch()
    {
        $this->voterFacade::fetch();
    }

    public function forceDelete(Request $request)
    {
        return response($this->voterFacade::forceDeleteMultiple($request));
    }

    public function restore(Request $request)
    {
        return response($this->voterFacade::restoreMultiple($request));
    }

    public function taggingIndex(Request $request)
    {
        $brgy_access = $hide_fields = [];
        $has_export = $has_area_search = $for_viewing_only = $can_clear_field = false;
        $restriction_properties = Auth::user()->restriction_properties;
        if (!empty($restriction_properties)) {
            $restriction_props = json_decode($restriction_properties,  true);
            if (isset($restriction_props["has_export"])) {
                $has_export = $restriction_props["has_export"];
            }
            if (isset($restriction_props["has_area_search"])) {
                $has_area_search = $restriction_props["has_area_search"];
            }
            if (isset($restriction_props["for_viewing_only"])) {
                $for_viewing_only = $restriction_props["for_viewing_only"];
            }
            if (
                isset($restriction_props["brgy_access"])
                && !empty($restriction_props["brgy_access"])
                && is_array($restriction_props["brgy_access"])
            ) {
                $brgy_access = $restriction_props["brgy_access"];
            }
            if (
                isset($restriction_props["hide_fields"])
                && !empty($restriction_props["hide_fields"])
                && is_array($restriction_props["hide_fields"])
            ) {
                $hide_fields = $restriction_props["hide_fields"];
            }
            if (isset($restriction_props["can_clear_field"])) {
                $can_clear_field = $restriction_props["can_clear_field"];
            }
        }

        if ($request->ajax()) {

            $voters = $this->voterFacade::getAllVoterTagDetails($request);

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
            $party_lists_1 = $this->tagFacade::getTags('party_list_1');
            $positions = $this->tagFacade::getTags('position');

            return view(
                'guest.voter.tagging',
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
                    'has_export',
                    'hide_fields',
                    'party_lists',
                    'has_area_search',
                    'for_viewing_only',
                    'party_lists_1',
                    'can_clear_field',
                    'positions',
                )
            );
        }
    }

    public function updateTagDetail(Request $request, VoterTagDetail $voter_tag_detail)
    {
        $response  = ["status" => 0, "message" => ""];

        $before = $voter_tag_detail->getAttributes();

        $brgy_access = [];
        $bypass_update = false;
        $restriction_properties = Auth::user()->restriction_properties;
        
        try {

            if (!empty($restriction_properties)) {
                $restriction_props = json_decode($restriction_properties,  true);
                if (
                    isset($restriction_props["brgy_access"])
                    && !empty($restriction_props["brgy_access"])
                    && is_array($restriction_props["brgy_access"])
                ) {
                    $brgy_access = $restriction_props["brgy_access"];
                }
    
                if (isset($restriction_props["bypass_update"])) {
                    $bypass_update = $restriction_props["bypass_update"];
                }
    
                if (isset($restriction_props["bypass_update"])) {
                    $bypass_update = $restriction_props["bypass_update"];
                }

                if (isset($restriction_props["for_viewing_only"]) && $restriction_props["for_viewing_only"]) {
                    throw new Exception("You are not allowed to update records");
                }
            }
    
            // if they will update the record with no affiliation or if CBH - temporary cond
            if ($request->field_name == "affiliation") {
                if (empty($voter_tag_detail->affiliation) || $voter_tag_detail->affiliation == "CBH") {
                    $bypass_update = true;
                }
            }

            if (!$voter_tag_detail) {
                throw new Exception("Voter not found");
            }

            if (in_array($voter_tag_detail->brgy, $brgy_access)) {
                $bypass_update = true;
            }

            if (
                !$bypass_update
                && $voter_tag_detail
                && $voter_tag_detail->last_update_by !== 0
                && $voter_tag_detail->last_update_by !== Auth::user()->id
            ) {
                throw new Exception("This record was already updated by another user (" . $voter_tag_detail->user->full_name . ")");
            }

            // @TODO delete this if not needed. This is only a temporary solution for blocking the update feature
            /*
            if (in_array(Auth::user()->email, [
                    "EncoderGAB@gmail.com",
                    "EncoderGAB1@gmail.com",
                    "EncoderGAB2@gmail.com",
                ])) {
                    throw new Exception("This record was already updated by another user.");
            }
            */

            if (!$voter_tag_detail) {
                throw new Exception("Tag detail not found");
            }

            $old_value = $voter_tag_detail->{$request->field_name};
            $affected = $voter_tag_detail->update([
                $request->field_name => $request->field_value ?? "",
                "last_update_by" => Auth::user() ? Auth::user()->id : 0,
            ]);
            if ($affected < 0) {
                throw new Exception("Nothing to update");
            }
            $response["status"] = 1;
            $response["message"] = "success";

            Log::info("Voter tag update success | Voter tag id : {$voter_tag_detail->id} | {$request->field_name} | FROM: $old_value | TO: {$request->field_value} | updated by : " . Auth::user()->full_name);

            $this->logUpdate(
                'Voter Tag Update',
                'Voter: ' . $voter_tag_detail->full_name,
                $before,
                $voter_tag_detail->getAttributes()
            );
        } catch (Exception $e) {
            report($e);
            $response["message"] = $e->getMessage();
        }
        return $response;
    }

    public function exportVoterTagDetails(Request $request)
    {
        $restriction_properties = Auth::user()->restriction_properties;

        Log::info("Checking Export Access on Voter Tagging | User: " . Auth::user()->full_name);

        if (!empty($restriction_properties)) {
            $restriction_props = json_decode($restriction_properties,  true);
            if(isset($restriction_props['has_export'])) {
                $has_export = $restriction_props['has_export'];
                if(!$has_export) {
                    abort(403, "You are not allowed to export records");
                }
            } else {
                abort(403, "You are not allowed to export records");
            }
        }

        Log::info("Export Voter Tag Details | User: " . Auth::user()->full_name);

        return $this->voterFacade::getAllVoterTagDetails($request, true);
    }

    public function clearFieldTag(Request $request)
    {
        $field = $request->field;
        $password = $request->password;
        $auth = Auth::user();

        $can_clear_field = false;
        $restriction_properties = $auth->restriction_properties;
                
        if (!empty($restriction_properties)) {
            $restriction_props = json_decode($restriction_properties,  true);
            if (isset($restriction_props["can_clear_field"])) {
                $can_clear_field = $restriction_props["can_clear_field"];
            }
        }

        // Only allow clearing fields that are fillable
        $fillable = (new \App\VoterTagDetail())->getFillable();
        if (!in_array($field, $fillable)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid field selected.'
            ], 400);
        }

        // Validate password
        if (!Hash::check($password, $auth->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid password'
            ], 403);
        }

        if (!$can_clear_field) {
            return response()->json([
                'status' => 0,
                'message' => 'Permission denied.'
            ], 400);
        }

        try {
            \App\VoterTagDetail::whereNotNull($field)
                ->when(!empty($request->field_filter) && !empty($request->field_value), fn($q) => $q->where($request->field_filter, $request->field_value))
                ->update([$field => null]);
            Log::info("Voter tag field cleared | Field: {$field} | Cleared by: " . $auth->full_name);
            return response()->json([
                'status' => 1,
                'message' => 'Field cleared successfully'
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json([
                'status' => 0,
                'message' => 'Error clearing field: ' . $e->getMessage()
            ], 500);
        }
    }

    public function voterAssistancesIndex(Request $request)
    {
        $brgy_access = $hide_fields = [];
        if ($request->ajax()) {

            $voters = $this->voterFacade::getAllVoterTagDetails($request);

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
                'guest.voter.tagging',
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
}
