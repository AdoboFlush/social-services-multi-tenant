<?php

namespace App\Services\Voter;

use App\Voter;
use App\Services\BaseService;
use App\VoterTagDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VoterService extends BaseService
{

    public const MINIMUM_SENIOR_CITIZEN_AGE = 60;
    private $allSearchFields = ['brgy', 'civil_status', 'full_name', 'religion', 'alliance', 'alliance_1', 'affiliation'];
    public function __construct() {}

    public function store(Request $request)
    {

        try {

            // Validation here

            $model = new Voter;
            $model->first_name = $request->first_name;
            $model->last_name = $request->last_name;
            $model->middle_name = !empty($request->middle_name) ? $request->middle_name : '';
            $model->suffix = !empty($request->suffix) ? $request->suffix : '';
            $model->birth_date = !empty($request->birth_date) ? $request->birth_date : '';
            $model->gender = !empty($request->gender) ? $request->gender : '';
            $model->precinct = !empty($request->precinct) ? $request->precinct : '';
            $model->brgy = !empty($request->brgy) ? $request->brgy : '';
            $model->address = !empty($request->address) ? $request->address : '';
            $model->alliance = !empty($request->alliance) ? ($request->alliance == 'Other' ? $request->alliance_text : $request->alliance) : '';
            $model->alliance_subgroup = !empty($request->alliance_subgroup) ? $request->alliance_subgroup : '';
            $model->alliance_1 = !empty($request->alliance_1) ? ($request->alliance_1 == 'Other' ?  $request->alliance_1_text : $request->alliance_1) : '';
            $model->alliance_1_subgroup = !empty($request->alliance_1_subgroup) ? $request->alliance_1_subgroup : '';
            $model->affiliation = !empty($request->affiliation) ? ($request->affiliation == 'Other' ? $request->affiliation_text : $request->affiliation) : '';
            $model->affiliation_subgroup = !empty($request->affiliation_subgroup) ? $request->affiliation_subgroup : '';
            $model->affiliation_1 = !empty($request->affiliation_1) ? ($request->affiliation_1 == 'Other' ? $request->affiliation_1_text : $request->affiliation_1) : '';
            $model->affiliation_1_subgroup = !empty($request->affiliation_1_subgroup) ? $request->affiliation_1_subgroup : '';
            $model->sectoral = !empty($request->sectoral) ? ($request->sectoral == 'Other' ? $request->sectoral_text : $request->sectoral) : '';
            $model->sectoral_1 = !empty($request->sectoral_1) ? ($request->sectoral_1 == 'Other' ? $request->sectoral_1_text : $request->sectoral_1) : '';
            $model->civil_status = !empty($request->civil_status) ? $request->civil_status : '';
            $model->beneficiary = !empty($request->beneficiary) ? $request->beneficiary : '';
            $model->religion = !empty($request->religion) ? $request->religion : '';
            $model->contact_number = !empty($request->contact_number) ? $request->contact_number : '';
            $model->remarks = !empty($request->remarks) ? $request->remarks : '';
            if (!empty($request->suffix)) {
                $model->full_name = $request->last_name . ", " . $request->first_name . " " . $request->suffix . " " . $request->middle_name;
            } else {
                $model->full_name = $request->last_name . ", " . $request->first_name . " " . $request->middle_name;
            }
            $affected_rows = $model->save();
            $model->refresh();

            activity("Create Voter")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Create new voter record');
            return redirect('voters')->with('success', 'Record has been inserted!');
        } catch (Exception $e) {
            report($e);
        }

        return redirect('voters')->with('error', 'Record insert failed');
    }

    public function update(Request $request)
    {

        try {

            // Validation here
            $model = Voter::find($request->voter_id);
            if (!$model) {
                return redirect('voters')->with('error', 'Record not found. Update failed');
            }
            $model->first_name = $request->first_name;
            $model->last_name = $request->last_name;
            $model->middle_name = !empty($request->middle_name) ? $request->middle_name : '';
            $model->suffix = !empty($request->suffix) ? $request->suffix : '';
            $model->birth_date = !empty($request->birth_date) ? $request->birth_date : '';
            $model->gender = !empty($request->gender) ? $request->gender : '';
            $model->precinct = !empty($request->precinct) ? $request->precinct : '';
            $model->brgy = !empty($request->brgy) ? $request->brgy : '';
            $model->address = !empty($request->address) ? $request->address : '';
            $model->alliance = !empty($request->alliance) ? ($request->alliance == 'Other' ? $request->alliance_text : $request->alliance) : (!empty($request->alliance_text) ? $request->alliance_text : '');
            $model->alliance_subgroup = !empty($request->alliance_subgroup) ? $request->alliance_subgroup : '';
            $model->alliance_1 = !empty($request->alliance_1) ? ($request->alliance_1 == 'Other' ?  $request->alliance_1_text : $request->alliance_1) : (!empty($request->alliance_1_text) ? $request->alliance_1_text : '');
            $model->alliance_1_subgroup = !empty($request->alliance_1_subgroup) ? $request->alliance_1_subgroup : '';
            $model->affiliation = !empty($request->affiliation) ? ($request->affiliation == 'Other' ? $request->affiliation_text : $request->affiliation) : (!empty($request->affiliation_text) ? $request->affiliation_text : '');
            $model->affiliation_subgroup = !empty($request->affiliation_subgroup) ? $request->affiliation_subgroup : '';
            $model->affiliation_1 = !empty($request->affiliation_1) ? ($request->affiliation_1 == 'Other' ? $request->affiliation_1_text : $request->affiliation_1) : (!empty($request->affiliation_1_text) ? $request->affiliation_1_text : '');
            $model->affiliation_1_subgroup = !empty($request->affiliation_1_subgroup) ? $request->affiliation_1_subgroup : '';
            $model->sectoral = !empty($request->sectoral) ? ($request->sectoral == 'Other' ? $request->sectoral_text : $request->sectoral) : (!empty($request->sectoral_text) ? $request->sectoral_text : '');
            $model->sectoral_1 = !empty($request->sectoral_1) ? ($request->sectoral_1 == 'Other' ? $request->sectoral_1_text : $request->sectoral_1) : (!empty($request->sectoral_1_text) ? $request->sectoral_1_text : '');
            $model->civil_status = !empty($request->civil_status) ? $request->civil_status : '';
            $model->beneficiary = !empty($request->beneficiary) ? $request->beneficiary : '';
            $model->religion = !empty($request->religion) ? $request->religion : '';
            $model->contact_number = !empty($request->contact_number) ? $request->contact_number : '';
            $model->remarks = !empty($request->remarks) ? $request->remarks : '';

            if (!empty($request->suffix)) {
                $model->full_name = $request->last_name . ", " . $request->first_name . " " . $request->suffix . " " . $request->middle_name;
            } else {
                $model->full_name = $request->last_name . ", " . $request->first_name . " " . $request->middle_name;
            }

            $affected_rows = $model->save();
            activity("Update Voter")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Update voter record');
            return redirect('voters')->with('success', 'Record has been updated!');
        } catch (Exception $e) {
            report($e);
        }

        return redirect('voters')->with('error', 'Record update failed');
    }

    public function getById(Request $request)
    {
        return Voter::find($request->id);
    }

    public function getAll(Request $request, bool $is_archived = false)
    {
        $model = new Voter;
        if ($is_archived) {
            $model = $model->onlyTrashed();
        }
        $model = $this->buildDataTableFilter($model, $request, true, $this->allSearchFields);
        if($request->has("a_query") && !empty($request->a_query) && is_array($request->a_query)) {
            foreach($request->a_query as $field => $value) {
                $model = $model->where($field, $value);
            }
        }
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        return [
            "data" => $model->get(),
            "count" => $total_count,
        ];
    }

    public function getAllVoterTagDetails(Request $request, $is_export = false, $assistance_event = null, $count_only = false)
    {
        $model = new VoterTagDetail();
        if ($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                if ($field == 'assistance_claimed') {
                    if (is_numeric($value)) {
                        $model = $model->whereHas('assistances', function ($query) use ($value, $assistance_event) {
                            $query->where('assistance_event_id', $assistance_event->id)
                                ->havingRaw('COUNT(*) = ?', [intval($value)]);
                        });
                    }
                } else {
                    $model = $model->where($field, 'LIKE', "%{$value}%");
                }
            }
        }

        if ($assistance_event && $assistance_event->custom_condition_props) {
            $custom_props = explode(";", $assistance_event->custom_condition_props);
            foreach ($custom_props as $custom_prop) {
                $prop = explode("=", $custom_prop);
                if (isset($prop[1])) {
                    $model = $model->where($prop[0], $prop[1]);
                }
            }
        }

        $restriction_properties = Auth::user()->restriction_properties;
        if (!empty($restriction_properties)) {
            $restriction_props = json_decode($restriction_properties,  true);
            if (isset($restriction_props["area"]) && !empty($restriction_props["area"])) {
                $brgy_restrictions = config('area_barangays')[$restriction_props["area"]];
                $model = $model->whereIn("brgy", $brgy_restrictions);
            }

            if (
                isset($restriction_props["affiliations"])
                && !empty($restriction_props["affiliations"])
                && is_array($restriction_props["affiliations"])
            ) {
                $restriction_props["affiliations"][] = "";
                $model = $model->whereIn("affiliation", $restriction_props["affiliations"]);
            }

            if (
                isset($restriction_props["excluded_affiliations"])
                && !empty($restriction_props["excluded_affiliations"])
                && is_array($restriction_props["excluded_affiliations"])
            ) {
                $model = $model->whereNotIn("affiliation", $restriction_props["excluded_affiliations"]);
            }

            if (
                isset($restriction_props["brgy_access"])
                && !empty($restriction_props["brgy_access"])
                && is_array($restriction_props["brgy_access"])
            ) {
                $restriction_props["brgy_access"][] = "";
                $model = $model->whereIn("brgy", $restriction_props["brgy_access"]);
            }

            if (isset($restriction_props["has_area_search"]) && $request->has("filter_area")) {
                $brgy_restrictions = config('area_barangays')[$request->filter_area];
                $model = $model->whereIn("brgy", $brgy_restrictions);
            }
        }

        if($request->has("filter_status") && !empty($request->filter_status)) {
            $model = $model->where("is_deceased", $request->filter_status == "deceased" ? 1 : 0);
        }   

        $model = $this->buildDataTableFilter($model, $request, true, $this->allSearchFields);
        $total_count = $model->count();

        if($count_only) {
            return $total_count;
        }

        if ($assistance_event) {
            $model = $model->withCount([
                'assistances' => fn($query) =>
                $query->where('assistance_event_id', $assistance_event->id)
            ]);
        } else {
            $model = $model->withCount('assistances');
        }
        $model = $this->buildModelQueryDataTable($model, $request);

        if ($is_export) {
            $columns = [
                'full_name',
                'birth_date',
                'gender',
                'precinct',
                'brgy',
                'address',
                'alliance',
                'alliance_1',
                'party_list',
                'affiliation',
                'affiliation_subgroup',
                'affiliation_1',
                'sectoral',
                'sectoral_subgroup',
                'religion',
                'civil_status',
                'contact_number',
                'remarks',
            ];
            return response()->streamDownload(
                function () use ($columns, $model) {
                    echo implode(",", $columns) . "\r\n";
                    $model->chunk(100, function ($voter_tags) use ($columns) {
                        echo $voter_tags
                            ->map(fn($voter_tag) => parseRowToCsv($voter_tag, collect($columns)))
                            ->implode("\r\n") . "\r\n";
                    });
                }
            );
        } else {
            return [
                "data" => $model->get(),
                "count" => $total_count,
            ];
        }
    }


    public function getAllSeniorCitizenVoters(Request $request)
    {
        $model = new Voter;
        $model = $model->where('birth_date', '<=', date('Y-m-d', strtotime('-' . self::MINIMUM_SENIOR_CITIZEN_AGE . ' years')));
        $model = $this->buildDataTableFilter($model, $request, true, $this->allSearchFields);
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        return [
            "data" => $model->get(),
            "count" => $total_count,
        ];
    }

    public function getTotalCount(Request $request)
    {
        $model = new Voter;
        $model = $this->buildDataTableFilter($model, $request, true, $this->allSearchFields);
        return $model->count();
    }

    public function getSeniorCitizenTotalCount(Request $request)
    {
        $model = new Voter;
        $model = $model->where('birth_date', '<=', date('Y-m-d', strtotime('-' . self::MINIMUM_SENIOR_CITIZEN_AGE . ' years')));
        $model = $this->buildDataTableFilter($model, $request, true, $this->allSearchFields);
        return $model->count();
    }

    public function generateSeniorCitizenCSV()
    {
        $model = new Voter;
        $model = $model->where('birth_date', '<=', date('Y-m-d', strtotime('-' . self::MINIMUM_SENIOR_CITIZEN_AGE . ' years')));
        $voters = $model->get();
        $file = fopen(public_path() . "/uploads/senior-citizens-" . date('Y-m-d') . ".csv", "w");
        fputcsv($file, ["full_name", "birth_date", "age", "gender", "precinct", "address", "brgy", "alliance", "affiliation", "religion", "civil_status", "contact_number", "remarks"]);
        foreach ($voters as $voter) {
            $line = [$voter->full_name, $voter->birth_date, $voter->age, $voter->gender, $voter->precinct, $voter->address, $voter->brgy, $voter->alliance, $voter->affiliation, $voter->religion, $voter->civil_status, $voter->contact_number, $voter->remarks];
            fputcsv($file, $line);
        }
        fclose($file);
    }

    public function getVoterDemographics(string $field, int $limit = 0)
    {
        $model = new Voter;
        $model = $model->selectRaw(' ' . $field . ', COUNT(*) as count ')
            ->groupBy([$field])
            ->orderBy($field, 'asc');
        if ($limit > 0) {
            $model = $model->limit($limit);
        }
        return $model->get();
    }

    public function search(Request $request)
    {
        $model = new Voter;
        if ($request->has('q')) {
            $model = $model->where('full_name', 'LIKE', $request->q . '%');
        }
        if ($request->has('page')) {
            $offset = 0;
            $limit = 10;
            if ($request->page > 1) {
                $offset +=  $limit * $request->page;
                $model = $model->offset($offset)->limit($limit);
            }
        }
        $model = $model->get()->map(function ($data) {
            return ['id' => $data->id, 'text' => $data->full_name];
        });
        return $model;
    }

    public function import(Request $request)
    {

        Log::info('Start Importing Voters CSV');

        set_time_limit(300);

        $file = $request->file("csv_file");
        $fileName = 'import_' . time() . '.' . $file->getClientOriginalExtension();
        $originalFile = fopen($file, "r");
        activity()->disableLogging();


        $counter = $successCount = $dupCount = $emptyCount = $failed = 0;

        while (($column = fgetcsv($originalFile, 50000, ",")) !== false) {

            try {

                $column = array_map("utf8_encode", $column);

                // 0 - full name, 1 - address, 2 - birthdate, 3 - gender, 4 - precinct
                $model = new Voter;
                $counter++;
                // if($counter === 1 || empty(trim($column[0]))){
                // Log::info('Skipped header row '.$counter);
                // continue; // skip the header
                // }

                if (empty(trim($column[0]))) {
                    Log::info('Skipped row - no column 0' . $counter);
                    $emptyCount++;
                    continue; // skip the header
                }

                $name_frag = explode(",", $column[0]);

                if (count($name_frag) !== 2) {
                    Log::info('Skipped row - not a valid name : ' . $column[0]);
                    $failed++;
                    continue; // skip the header
                }

                $last_name = trim($name_frag[0]);
                $partial_name_frag = explode(" ", $name_frag[1]);
                $first_name = [];
                for ($i = 0; $i < count($partial_name_frag) - 1; $i++) {
                    $first_name[] = $partial_name_frag[$i];
                }
                $birth_date_frag = explode("/", $column[2]);
                if (count($birth_date_frag) !== 3) {
                    continue; // skip the header
                }

                DB::beginTransaction();

                $birth_date = $birth_date_frag[2] . "-" . $birth_date_frag[0] . "-" . $birth_date_frag[1];
                $first_name = implode(" ", $first_name);
                $middle_name = $partial_name_frag[count($partial_name_frag) - 1];
                $model->first_name = $first_name;
                $model->middle_name = $middle_name;
                $model->last_name = $last_name;
                $model->suffix = '';
                $model->full_name = @$column[0];
                $model->address = @$column[1];
                $model->gender = @$column[3];
                $model->birth_date = $birth_date;
                $model->precinct = @$column[4];
                $model->brgy = @$column[5];
                $affected_rows = $model->save();
                if ($affected_rows > 0) {
                    $successCount++;
                }

                DB::commit();
            } catch (QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    $dupCount++;
                }
            } catch (Exception $e) {
                DB::rollBack();
                $failed++;
                report($e);
                $message = $this->getErrorMessage($e);
                return back()->with('error', _lang('Unexpected Error Occurred: ' .  $message));
            }
        }

        activity()->enableLogging();
        activity("Voters Import")
            ->causedBy(Auth::user())
            ->performedOn($model)
            ->withProperties($model)
            ->log('Import Voters CSV File');

        $archivePath = public_path() . "/uploads/import_request/";
        if (!file_exists($archivePath)) {
            mkdir($archivePath);
        }
        $file->move($archivePath, $fileName);

        return redirect('voters')
            ->withSuccess("Import Voters CSV Made Successfully")
            ->withMessages([
                "Total Records: {$counter}",
                "Inserted: {$successCount}",
                "Already exists in Voters: {$dupCount}",
                "Empty Rows: {$emptyCount}",
                "Failed: {$failed}",
            ]);
    }

    public function fetchVotersV2($file_path = "data/2025", $verbose = false)
    {
        Log::info('Start Importing Voters CSV - MANUAL');
        $file_path = "data/2024";

        $files = scandir(storage_path($file_path));
        array_shift($files);
        array_shift($files);

        foreach ($files as $file) {
            $csvFile = storage_path($file_path) . "/" . $file;
            if (!file_exists($csvFile)) {
                echo "$csvFile not found";
                return "$csvFile not found";
            }
            echo "\n Parsing $csvFile";

            $brgy = trim(str_replace(".csv", "", trim(preg_replace('/[0-9]+/', '', $file))));

            $file = fopen($csvFile, "r");
            $counter = 0;
            while (($column = fgetcsv($file, 500000, ",")) !== false) {

                try {

                    $column = array_map("utf8_encode", $column);

                    $counter++;
                    if ($counter === 1 || empty(trim($column[0]))) {
                        continue; // skip the header
                    }

                    $full_name = trim(preg_replace('/[0-9]+/', '', $column[0]));
                    $address = trim($column[1]);
                    $birth_date = $this->handleDateValues($column[2]);
                    $gender = $this->handleGenderValue($column[3]);
                    $precinct = trim($column[4]);

                    if (empty($birth_date)) {
                        continue;
                    }

                    $name_frag = explode(",", $full_name);
                    if (count($name_frag) < 2) {
                        continue; // skip if not a name
                    }

                    if (count($name_frag) == 2) {
                        $last_name = trim($name_frag[0]);
                        $partial_name_frag = explode(" ", $name_frag[1]);
                        $first_name = [];
                        for ($i = 0; $i < count($partial_name_frag) - 1; $i++) {
                            $first_name[] = $partial_name_frag[$i];
                        }
                        $first_name = trim(implode(" ", $first_name));
                        $middle_name = $partial_name_frag[count($partial_name_frag) - 1];
                        $suffix = '';
                    } elseif (count($name_frag) == 3) {
                        $last_name = trim($name_frag[0]);
                        $first_name = trim($name_frag[1]);
                        $partial_name_frag = explode(" ", $name_frag[2]);
                        $suffix = trim($partial_name_frag[1]);
                        $middle_name_frag = explode($suffix, $name_frag[2]);
                        $middle_name = trim($middle_name_frag[1]);
                    }

                    if ($verbose) {
                        echo "\n" . json_encode([
                            "full_name" => $full_name,
                            "precinct" => $precinct,
                            "brgy" => $brgy,
                            "birth_date" => $birth_date,
                            "gender" => $gender,
                            "address" => $address,
                            "first_name" => $first_name,
                            "last_name" => $last_name,
                            "middle_name" => $middle_name,
                            "suffix" => $suffix,
                        ]);
                    }

                    $voter = Voter::updateOrCreate([
                        "full_name" => $full_name,
                        "birth_date" => $birth_date,
                        "precinct" => $precinct,
                        "gender" => $gender,
                        "brgy" => $brgy,
                    ], [
                        "address" => $address,
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "middle_name" => $middle_name,
                        "suffix" => $suffix,
                    ]);
                } catch (QueryException $e) {
                    $errorCode = $e->errorInfo[1];
                    if ($errorCode == 1062) {
                        echo "\n DUPLICATE ENTRY " . implode($column);
                    }
                } catch (Exception $e) {
                    $message = $this->getErrorMessage($e);
                    echo "\n ERROR:  {$message}";
                }
            }
        }
    }

    public function upsertVoterTagDetails($file_path = "data/voter_tagging", $verbose = false)
    {
        Log::info('Start Upserting Voter Tag Details CSV - MANUAL');

        $files = scandir(storage_path($file_path));
        array_shift($files);
        array_shift($files);

        foreach ($files as $file) {
            $csvFile = storage_path($file_path) . "/" . $file;
            if (!file_exists($csvFile)) {
                echo "$csvFile not found";
                return "$csvFile not found";
            }
            echo "\n Parsing $csvFile";

            $brgy = trim(str_replace(".csv", "", trim(preg_replace('/[0-9]+/', '', $file))));

            $file = fopen($csvFile, "r");
            $counter = 0;
            while (($column = fgetcsv($file, 500000, ",")) !== false) {

                try {

                    $column = array_map("utf8_encode", $column);

                    $counter++;
                    if ($counter === 1 || empty(trim($column[0]))) {
                        continue; // skip the header
                    }

                    $full_name = trim(preg_replace('/[0-9]+/', '', $column[0]));
                    $address = trim($column[1]);
                    $birth_date = $this->handleDateValues($column[2]);
                    $gender = $this->handleGenderValue($column[3]);
                    $precinct = trim($column[4]);

                    if (empty($birth_date)) {
                        continue;
                    }

                    $name_frag = explode(",", $full_name);
                    if (count($name_frag) < 2) {
                        continue; // skip if not a name
                    }

                    if (count($name_frag) == 2) {
                        $last_name = trim($name_frag[0]);
                        $partial_name_frag = explode(" ", $name_frag[1]);
                        $first_name = [];
                        for ($i = 0; $i < count($partial_name_frag) - 1; $i++) {
                            $first_name[] = $partial_name_frag[$i];
                        }
                        $first_name = trim(implode(" ", $first_name));
                        $middle_name = $partial_name_frag[count($partial_name_frag) - 1];
                        $suffix = '';
                    } elseif (count($name_frag) == 3) {
                        $last_name = trim($name_frag[0]);
                        $first_name = trim($name_frag[1]);
                        $partial_name_frag = explode(" ", $name_frag[2]);
                        $suffix = trim($partial_name_frag[1]);
                        $middle_name_frag = explode($suffix, $name_frag[2]);
                        $middle_name = trim($middle_name_frag[1]);
                    }

                    if ($verbose) {
                        echo "\n" . json_encode([
                            "full_name" => $full_name,
                            "precinct" => $precinct,
                            "brgy" => $brgy,
                            "birth_date" => $birth_date,
                            "gender" => $gender,
                            "address" => $address,
                            "first_name" => $first_name,
                            "last_name" => $last_name,
                            "middle_name" => $middle_name,
                            "suffix" => $suffix,
                        ]);
                    }

                    $key_details = [
                        "full_name" => $full_name,
                        "birth_date" => $birth_date,
                        "precinct" => $precinct,
                        "gender" => $gender,
                        "brgy" => $brgy,
                    ];

                    $voter = Voter::where($key_details)->first();

                    $name_parts = parse_name($full_name);
                    $first_name = $name_parts['first_name'] ?? '';
                    $last_name = $name_parts['last_name'] ?? '';
                    $middle_name = $name_parts['middle_name'] ?? '';
                    $suffix = $name_parts['suffix'] ?? '';

                    $voter_detail = VoterTagDetail::updateOrCreate([
                        "full_name" => $full_name,
                        "birth_date" => $birth_date,
                        "precinct" => $precinct,
                        "gender" => $gender,
                        "brgy" => $brgy,
                    ], [
                        "address" => $address,
                        "alliance" => $voter ? $voter->alliance : "",
                        "affiliation" => $voter ? $voter->affiliation : "",
                        "contact_number" => $voter ? $voter->contact_number : "",
                        "civil_status" => $voter ? $voter->civil_status : "",
                        "religion" => $voter ? $voter->religion : "",
                        'alliance_subgroup' => $voter ? $voter->alliance_subgroup : "",
                        'alliance_1' => $voter ? $voter->alliance_1 : "",
                        'alliance_1_subgroup' => $voter ? $voter->alliance_1_subgroup : "",
                        'affiliation_subgroup' => $voter ? $voter->affiliation_subgroup : "",
                        'affiliation_1' => $voter ? $voter->affiliation_1 : "",
                        'affiliation_1_subgroup' => $voter ? $voter->affiliation_1_subgroup : "",
                        'sectoral' => $voter ? $voter->sectoral : "",
                        'sectoral_1' => $voter ? $voter->sectoral_1 : "",
                        "voter_id" => $voter ? $voter->id : 0,
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "middle_name" => $middle_name,
                        "suffix" => $suffix,
                    ]);
                } catch (QueryException $e) {
                    $errorCode = $e->errorInfo[1];
                    if ($errorCode == 1062) {
                        echo "\n DUPLICATE ENTRY " . implode($column);
                    }
                } catch (Exception $e) {
                    $message = $this->getErrorMessage($e);
                    echo "\n ERROR:  {$message}";
                }
            }
        }

        Log::info('End Upserting Voter Tag Details CSV - MANUAL');
    }

    private function handleNullValues($value)
    {
        if (!empty($value)) {
            return trim($value);
        }
        return "";
    }

    private function handleGenderValue($value)
    {
        if (in_array($value, ["M", "F", "FEMALE", "MALE"])) {
            if ($value === "MALE") return "M";
            if ($value === "FEMALE") return "F";
            return $value;
        }
        return "";
    }

    private function handleDateValues($date)
    {
        try {
            if (strpos($date, "/") !== false) {
                $date_frag = explode("/", $date);
                if (count($date_frag) != 3 || strlen($date_frag[2]) !== 4) {
                    Log::error("INVALID DATE FORMAT : {$date}");
                    return null; // Invalid Date Format
                }
                $date = $date_frag[2] . "-" . $date_frag[1] . "-" . $date_frag[0];
                return Carbon::parse($date)->format("Y-m-d");
            } elseif (strpos($date, "-") !== false) {
                $date_frag = explode("-", $date);
                if (count($date_frag) != 3 || strlen($date_frag[0]) !== 4) {
                    Log::error("INVALID DATE FORMAT : {$date}");
                    return null; // Invalid Date Format
                }
                return Carbon::parse($date)->format("Y-m-d");
            }
        } catch (Exception $e) {
            Log::info("FOUND INVALID DATE ");
        }

        return null;
    }

    public function fetch()
    {

        Log::info('Start Importing Voters CSV - MANUAL');
        $csvFile = storage_path('data/csv') . "/voters.csv";
        if (!file_exists($csvFile)) {
            echo "$csvFile not found";
            return "$csvFile not found";
        }

        $file = fopen($csvFile, "r");
        $counter = $successCount = $dupCount = $emptyCount = 0;
        //DB::beginTransaction();

        while (($column = fgetcsv($file, 500000, ",")) !== false) {

            try {

                // 0 - full name, 1 - address, 2 - birthdate, 3 - gender, 4 - precinct
                $model = new Voter;
                $counter++;
                if ($counter === 1 || empty(trim($column[0]))) {
                    // Log::info('Skipped header row '.$counter);
                    continue; // skip the header
                }

                if (empty(trim($column[0]))) {
                    // Log::info('Skipped row '.$counter);
                    $emptyCount++;
                    continue; // skip the header
                }

                $column = array_map("utf8_encode", $column);

                $name_frag = explode(",", $column[2]);

                if (count($name_frag) < 2) {
                    // Log::info('Skipped row '.$counter);
                    // Log::info($column);
                    continue; // skip the header
                }

                if (count($name_frag) == 2) {
                    $last_name = trim($name_frag[0]);
                    $partial_name_frag = explode(" ", $name_frag[1]);
                    $first_name = [];
                    for ($i = 0; $i < count($partial_name_frag) - 1; $i++) {
                        $first_name[] = $partial_name_frag[$i];
                    }
                    $first_name = implode(" ", $first_name);
                    $middle_name = $partial_name_frag[count($partial_name_frag) - 1];
                    $suffix = '';
                } elseif (count($name_frag) == 3) {
                    $last_name = trim($name_frag[0]);
                    $first_name = trim($name_frag[1]);
                    $partial_name_frag = explode(" ", $name_frag[2]);
                    $suffix = trim($partial_name_frag[1]);
                    $middle_name_frag = explode($suffix, $name_frag[2]);
                    $middle_name = trim($middle_name_frag[1]);
                }


                $birth_date_frag = explode("/", $column[3]);
                if (count($birth_date_frag) !== 3) {
                    // Log::info('Skipped row '.$counter);
                    // Log::info($column);
                    continue; // skip the header
                }
                $birth_date = $birth_date_frag[2] . "-" . $birth_date_frag[1] . "-" . $birth_date_frag[0];


                $model->first_name = $first_name;
                $model->middle_name = $middle_name;
                $model->last_name = $last_name;
                $model->suffix = $suffix;

                $model->alliance = $column[6] == 'undefined' ? '' : $column[6];
                $model->affiliation = $column[7] == 'undefined' ? '' : $column[7];
                $model->brgy = $column[8] == 'undefined' ? '' : $column[8];

                $model->full_name = $column[2];
                $model->address = $column[9];
                $model->gender = $column[5];
                $model->birth_date = $birth_date;
                $model->precinct = $column[10];
                $affected_rows = $model->save();
                if ($affected_rows > 0) {
                    $successCount++;
                }
            } catch (QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    Log::info('Duplicate Entry : ' . json_encode($column));
                    $dupCount++;
                }
            } catch (Exception $e) {
                //DB::rollBack();
                report($e);
                $message = $this->getErrorMessage($e);
                return back()->with('error', _lang('Unexpected Error Occurred: ' .  $message));
            }
        }

        //DB::commit();
        Log::info('End Importing Voters CSV - MANUAL');
    }

    public function deleteMultiple(Request $request)
    {
        if ($request->has('selected_ids')) {
            foreach ($request->selected_ids as $selected_id) {
                $model = Voter::find($selected_id);
                activity("Archive Voter")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Archive Voter : ' . $model->full_name);
                $model->delete();
            }
            return 1;
        }
        return 0;
    }

    public function forceDeleteMultiple(Request $request)
    {
        if ($request->has('selected_ids')) {
            foreach ($request->selected_ids as $selected_id) {
                $model = Voter::withTrashed()->find($selected_id);
                activity("Force Delete Voter")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Force Delete Voter : ' . $model->full_name);
                $model->forceDelete();
            }
            return 1;
        }
        return 0;
    }

    public function restoreMultiple(Request $request)
    {
        if ($request->has('selected_ids')) {
            foreach ($request->selected_ids as $selected_id) {
                $model = Voter::withTrashed()->find($selected_id);
                activity("Restore Voter")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Restore Voter : ' . $model->full_name);
                $model->restore();
            }
            return 1;
        }
        return 0;
    }
}
