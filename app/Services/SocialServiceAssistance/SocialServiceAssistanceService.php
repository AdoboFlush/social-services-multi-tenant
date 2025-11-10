<?php

namespace App\Services\SocialServiceAssistance;

use App\Notifications\AppNotification;
use App\SocialServiceAssistance;
use App\Services\BaseService;
use App\Tag;
use App\User;
use App\Voter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class SocialServiceAssistanceService extends BaseService
{

    public const REQUEST_INTERVAL_IN_DAYS = 90;

    private const MAX_CHAR_CONTROL_NUMBER = 8;

    private const EXEMPTED_REQUEST_TYPES = ["RICE DISTRIBUTION"];

    public function __construct() {}

    public function store(Request $request)
    {
        $response_route = $request->has('event_id') && !empty($request->event_id) ? redirect("/events/show/{$request->event_id}") : back();

        try {

            $message = '';
            if ($request->has('amount')) {
                if (empty($request->amount)) {
                    $request->amount = 0;
                }
            }
            $validator = Validator::make($request->all(), [
                //'control_number' => 'required|numeric|digits:' . self::MAX_CHAR_CONTROL_NUMBER,
                'first_name' => 'required',
                'last_name' => 'required',
                'requestor_first_name' => 'required',
                'requestor_last_name' => 'required',
                'brgy' => 'required',
                'address' => 'required',
                'purpose' => 'required',
                'request_type_id' => 'required|numeric',
                'referred_by' => 'required',
                'processed_by' => 'required',
                'file_date' => 'required',
                'processed_date' => 'required',
                'amount' => 'numeric',
                'birth_date' => 'required'
            ]);

            if ($validator->fails()) {
                $message = _lang(($validator->errors()->first()));
                return back()->withInput()->with('error', $message);
            }

            $control_number = $this->generateControlNumber($request->request_type_id);
            
            $model = new SocialServiceAssistance;
            $model->control_number = $control_number;
            $model->first_name = $request->first_name;
            $model->middle_name =  !empty($request->middle_name) ? $request->middle_name : '';
            $model->last_name = $request->last_name;
            $model->suffix = !empty($request->suffix) ? $request->suffix : '';
            $model->contact_number = !empty($request->contact_number) ? $request->contact_number : '';
            $model->brgy = !empty($request->brgy) ? $request->brgy : '';
            $model->address = $request->address;
            $model->organization = !empty($request->organization) ? $request->organization : '';
            $model->purpose = json_encode($request->purpose);
            $model->referred_by = $request->referred_by;
            $model->processed_by = $request->processed_by;
            $model->received_by = !empty($request->received_by) ? $request->received_by : '';
            $model->file_date = $request->file_date;
            $model->precinct = $request->precinct;
            $model->processed_date = $request->processed_date;
            $model->amount = !empty($request->amount) ? $request->amount : 0;
            $model->request_type_id = $request->request_type_id;
            $model->requestor_last_name = $request->requestor_last_name;
            $model->requestor_first_name = $request->requestor_first_name;
            $model->requestor_middle_name = !empty($request->requestor_middle_name) ? $request->requestor_middle_name : '';
            $model->requestor_suffix = !empty($request->requestor_suffix) ? $request->requestor_suffix : '';
            $model->requestor_relationship_to_beneficiary = !empty($request->requestor_relationship_to_beneficiary) ? $request->requestor_relationship_to_beneficiary : '';
            $model->encoder_id = Auth::user()->id;
            $model->source = $request->source;

            if ($request->has("birth_date") && !empty($request->birth_date)) {
                $model->birth_date = $request->birth_date;
            }
            $model->is_deceased = $request->has("is_deceased");

            if ($request->has('is_voter')) {
                $model->is_voter = 1;
            } else {
                $model->is_voter = 0;
            }
            if ($request->has('same_with_beneficiary')) {
                $model->requestor_same_to_beneficiary = 1;
            } else {
                $model->requestor_same_to_beneficiary = 0;
            }

            // If its a voter record, update the contact number
            if($request->has('voter_id') && $request->has('contact_number')) {
                $affected_voter = Voter::where("id", $request->voter_id)
                    ->update(["contact_number" => $request->contact_number]);
            }

            $tag = Tag::find($request->request_type_id);
            $isForValidation = $isBeneficiaryForValidation = false;
            $isExemptedRequest = $tag && in_array($tag->name, self::EXEMPTED_REQUEST_TYPES);
            if(!$isExemptedRequest) {
                $isForValidation = $this->checkIfRequestorExists($request);
                if (!$isForValidation) {
                    $isBeneficiaryForValidation = $this->checkIfBeneficiaryExists($request);
                }
            }        
            $remarks = !empty($request->remarks) ? $request->remarks : '';

            if ($isForValidation) {
                $appMessage = 'This requestor has already an approved/released request within ' . self::REQUEST_INTERVAL_IN_DAYS . ' days. This will be tagged as For validation.';
                $model->status = SocialServiceAssistance::STATUS_FOR_VALIDATION;
                $remarks .= "\r\n\r\n[SYSTEM] " . $appMessage;
            } elseif ($isBeneficiaryForValidation) {
                $appMessage = 'This beneficiary has already an approved/released request within ' . self::REQUEST_INTERVAL_IN_DAYS . ' days. This will be tagged as For validation.';
                $model->status = SocialServiceAssistance::STATUS_FOR_VALIDATION;
                $remarks .= "\r\n\r\n[SYSTEM] " . $appMessage;
            } else {
                $appMessage = 'New Request has been created! Control #: ' . $control_number;
                $model->status = SocialServiceAssistance::STATUS_PENDING;
            }
            $model->remarks =  $remarks;

            if ($request->has('event_id') && !empty($request->event_id)) {
                $model->event_id = $request->event_id;
            }

            if($this->checkIfBeneficiaryExistsInEvent($request)) {
                throw new Exception('This beneficiary already exists in this event.');
            }

            $affected_rows = $model->save();

            $notificationMessage = "New Request Control # {$control_number} has created by " . Auth::user()->full_name;
            $admins = User::whereHas('roles', function ($query) {
                $query->where('id', 1);
                $query->where('name', 'admin');
            })->get();
            activity("Create New Social Service Request")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Create New Social Service Request. Control # ' . $control_number);

            $session_status = "";
            if ($isForValidation || $isBeneficiaryForValidation) {
                //Notification::send($admins, new AppNotification('[For Validation] New Social Service Request Created', $notificationMessage, $model, url('social_services')));
                $model->remarks .= "\r\n\r\n[SYSTEM] " . $appMessage;
                $session_status = 'warning';
            } else {
                //Notification::send($admins, new AppNotification('New Social Service Request Created', $notificationMessage, $model, url('social_services')));
                $session_status = 'success';
            }
            
            return $response_route->with($session_status, $appMessage);

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                report($e);
                $message = "Duplicate Control Number.";
            }
        } catch (Exception $e) {
            report($e);
            $message = $e->getMessage();
        }

        return $response_route->withInput()->with('error', 'Record insert failed - ' . $message);
    }

    public function update(Request $request)
    {

        try {

            $model = SocialServiceAssistance::find($request->social_service_id);
            if (!$model) {
                return redirect('social_services')->with('error', 'Record not found. Update failed');
            }

            if ($request->has('releasing')) {

                Log::info("Releasing Assistance Request Control Number #{$model->control_number} ");
                if ($request->has('received_by') && !empty($request->received_by))
                    $model->received_by = $request->received_by;

                if ($request->has('received_date') && !empty($request->received_date))
                    $model->received_date = $request->received_date;

                $model->release_date = date('Y-m-d');

                if ($request->has('amount') && !empty($request->amount))
                    $model->amount = $request->amount;

                $model->status = SocialServiceAssistance::STATUS_RELEASED;
                $model->releaser_id = Auth::user()->id;

                $model->save();
                activity("Update Social Service Request")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Releasing Social Service Request # ' . $model->control_number);

                $notificationMessage = "Request Control # {$model->control_number} has been released by " . Auth::user()->full_name;
                $admins = User::whereHas('roles', function ($query) {
                    $query->where('id', 1);
                    $query->where('name', 'admin');
                })->get();

                //Notification::send($admins, new AppNotification('Social Service Request Released', $notificationMessage, $model, url('social_services')));
                return back()->with('success', 'Record has been updated!');
            }

            $message = '';
            if ($request->has('amount')) {
                if (empty($request->amount)) {
                    $request->amount = 0;
                }
            }
            $validator = Validator::make($request->all(), [
                'control_number' => 'required|numeric|digits:' . self::MAX_CHAR_CONTROL_NUMBER,
                'first_name' => 'required',
                'last_name' => 'required',
                'requestor_first_name' => 'required',
                'requestor_last_name' => 'required',
                'brgy' => 'required',
                'address' => 'required',
                'purpose' => 'required',
                'referred_by' => 'required',
                'processed_by' => 'required',
                'file_date' => 'required',
                'processed_date' => 'required',
                'amount' => 'numeric',
                'social_service_id' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                $message = _lang(($validator->errors()->first()));
                return back()->withInput()->with('error', $message);
            }

            $changed_status = false;
            if ($request->has('previous_status') && $request->has('status')) {
                if ($request->previous_status != $request->status) {
                    $changed_status = true;
                }
            }

            $control_number = $request->control_number;
            if(intval($request->request_type_id) !== intval($model->request_type_id)) {
                // request type has changed, need to generate a new control number
                $control_number = $this->generateControlNumber($request->request_type_id);
            }

            if ($request->has('event_id') && !empty($request->event_id)) {
                $model->event_id = $request->event_id;
            }

            if($this->checkIfBeneficiaryExistsInEvent($request)) {
                throw new Exception('This beneficiary already exists in this event.');
            }

            $model->control_number = $control_number;
            $model->first_name = $request->first_name;
            $model->middle_name =  !empty($request->middle_name) ? $request->middle_name : '';
            $model->last_name = $request->last_name;
            $model->suffix = !empty($request->suffix) ? $request->suffix : '';
            $model->contact_number = !empty($request->contact_number) ? $request->contact_number : '';
            $model->brgy = $request->brgy;
            $model->address = $request->address;
            $model->organization = $request->organization;
            $model->purpose = json_encode($request->purpose);
            $model->referred_by = $request->referred_by;
            $model->processed_by = $request->processed_by;
            $model->file_date = $request->file_date;
            $model->processed_date = $request->processed_date;
            $model->amount = !empty($request->amount) ? $request->amount : 0;
            $model->remarks = !empty($request->remarks) ? $request->remarks : '';
            $model->approved_by = $request->approved_by;
            $model->request_type_id = $request->request_type_id;
            $model->requestor_last_name = $request->requestor_last_name;
            $model->requestor_first_name = $request->requestor_first_name;
            $model->precinct = $request->precinct;
            $model->source = $request->source;

            if ($request->has("birth_date") && !empty($request->birth_date)) {
                $model->birth_date = $request->birth_date;
            }
            $model->is_deceased = $request->has("is_deceased");

            if ($request->has('received_by') && !empty($request->received_by))
                $model->received_by = $request->received_by;

            if ($request->has('received_date') && !empty($request->received_date))
                $model->received_date = $request->received_date;

            if ($request->has('release_date') && !empty($request->release_date))
                $model->release_date = $request->release_date;

            $model->requestor_middle_name = !empty($request->requestor_middle_name) ? $request->requestor_middle_name : '';
            $model->requestor_suffix = !empty($request->requestor_suffix) ? $request->requestor_suffix : '';
            $model->requestor_relationship_to_beneficiary = !empty($request->requestor_relationship_to_beneficiary) ? $request->requestor_relationship_to_beneficiary : '';
            if ($request->has('status')) {
                $model->status = $request->status;
                if ($request->status == 'Approved') {
                    $model->approved_date = date('Y-m-d');
                    $model->approved_by = Auth::user()->id;
                }
            }
            if ($request->has('same_with_beneficiary')) {
                $model->requestor_same_to_beneficiary = 1;
            } else {
                $model->requestor_same_to_beneficiary = 0;
            }
            if ($request->has('is_voter')) {
                $model->is_voter = 1;
            } else {
                $model->is_voter = 0;
            }
            $affected_rows = $model->save();
            activity("Update Social Service Request")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Update Social Service Request. Control # ' . $model->control_number);

            if ($changed_status) {

                $encoder = User::find($request->encoder_id);
                $notificationMessage = "Request with Control # {$model->control_number} changed its status from {$request->previous_status} to {$request->status} by " . Auth::user()->full_name;

                //Notification::send($encoder, new AppNotification('Social Service Request Status Update', $notificationMessage, $model, url('social_services')));
                activity("Update Social Service Request")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Update Social Service Request Status. ' . $model->control_number . ' | Status: ' . $request->status);
            }

            $session_status = 'success';
            $appMessage = 'Request has been updated! Control #: ' . $control_number;
            if ($request->has('event_id') && !empty($request->event_id)) {
                return redirect("/events/show/{$request->event_id}")->with($session_status, $appMessage);
            }

            return back()->with($session_status, $appMessage);
        
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                report($e);
                $message = "Duplicate Control Number.";
            }
        } catch (Exception $e) {
            report($e);
            $message = $e->getMessage();
        }

        return back()->withInput()->with('error', 'Record insert failed - ' . $message);
    }

    public function getById(Request $request)
    {
        return SocialServiceAssistance::with(['encoder', 'approver', 'releaser'])->find($request->id);
    }

    public function getAll(Request $request, int $event_id = 0)
    {
        $model = new SocialServiceAssistance;
        $model = $model->with(['encoder', 'approver']);
        if ($request->has('search_status') && !empty($request->search_status)) {
            $model = $model->where('status', $request->search_status);
        } else {
            $model = $model->where('status', '!=', SocialServiceAssistance::STATUS_FOR_VALIDATION);
            $model = $model->where('status', '!=', SocialServiceAssistance::STATUS_FOR_DELETE);
        }
        if($event_id > 0) {
            $model = $model->where('event_id', $event_id);
        }
        $model = $this->buildDataTableFilter(
            $model,
            $request,
            true,
            ['first_name', 'last_name', 'middle_name', 'purpose'],
            ['purpose_text' => 'purpose'],
            ['encoder.full_name', 'approver.full_name']
        );

        if ($request->has('encoder_search') && !empty($request->encoder_search)) {
            $model = $model->where('encoder_id', $request->encoder_search);
        }

        if ($request->has('approver_search') && !empty($request->approver_search)) {
            $model = $model->where('approved_by', $request->approver_search);
        }

        if ($request->has('request_type_search') && !empty($request->request_type_search)) {
            $model = $model->where('request_type_id', $request->request_type_search);
        }

        if ($request->has('releaser_search') && !empty($request->releaser_search)) {
            $model = $model->where('releaser_id', $request->releaser_search);
        }

        if ($request->has('source_search') && !empty($request->source_search)) {
            $model = $model->where('source', $request->source_search);
        }

        if ($request->has('beneficiary_search') && !empty($request->beneficiary_search)) {
            $model = $model->whereRaw(
                "concat(last_name, ', ', first_name, ' ', middle_name, ' ', suffix) LIKE ?",
                ['%' . $request->beneficiary_search . '%']
            );
        }

        if ($request->has('requestor_search') && !empty($request->requestor_search)) {
            $model = $model->whereRaw(
                "concat(requestor_last_name, ', ', requestor_first_name, ' ', requestor_middle_name, ' ', requestor_suffix) LIKE ?",
                ['%' . $request->requestor_search . '%']
            );
        }

        $data_total = $model->count();

        $model = $this->buildModelQueryDataTable($model, $request, ['approver.full_name' => 'approved_by', 'encoder.full_name' => 'encoder_id']);
        return ["data" => $model->get(), "total" => $data_total];
    }

    public function getReportAll(Request $request, $isExport = false)
    {
        $model = new SocialServiceAssistance;
        if ($request->has('filter')) {
            $request_arr = $request->all();
            if (isset($request_arr['filter']['status'])) {
                $model = $model->where('status', $request_arr['filter']['status']);
            }
            if (isset($request_arr['filter']['request_type_id'])) {
                $model = $model->where('request_type_id', $request_arr['filter']['request_type_id']);
            }
            if (isset($request_arr['filter']['brgy'])) {
                $model = $model->where('brgy', $request_arr['filter']['brgy']);
            }
            if (isset($request_arr['filter']['is_voter'])) {
                $model = $model->where('is_voter', intval($request_arr['filter']['is_voter']));
            }
            if (isset($request_arr['filter']['source'])) {
                $model = $model->where('source', $request_arr['filter']['source']);
            }

            if (isset($request_arr['filter']['date_from']) && isset($request_arr['filter']['date_field'])) {
                if (isset($request_arr['filter']['date_to'])) {
                    $model = $model->whereBetween(
                        $request_arr['filter']['date_field'],
                        [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_to'] . " 23:59:59"]
                    );
                } else {
                    $model = $model->whereBetween(
                        $request_arr['filter']['date_field'],
                        [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_from'] . " 23:59:59"]
                    );
                }
            }
        }
        if ($isExport) {
            $columns = [
                'control_number',
                'full_name',
                'requestor_full_name',
                'brgy',
                'address',
                'request_type',
                'purpose_text',
                'created_at',
                'file_date',
                'processed_date',
                'release_date',
                'status',
                'amount'
            ];
            return response()->streamDownload(
                function () use ($columns, $model) {
                    echo implode(",", $columns) . "\r\n";
                    $model->chunk(50, function ($social_services) use ($columns) {
                        echo $social_services
                            ->map(fn($social_service) => parseRowToCsv($social_service, collect($columns)))
                            ->implode("\r\n") . "\r\n";
                    });
                }
            );
        } else {
            $data_total = $model->count();
            $model = $this->buildModelQueryDataTable($model, $request, ['approver.full_name' => 'approved_by', 'encoder.full_name' => 'encoder_id']);
            return ["data" => $model->get(), "total" => $data_total];
        }
    }

    public function getSocialServiceAssistances(string $type)
    {
        $model = new SocialServiceAssistance;
        $model = $model->where('type', $type)->where('status', 1);
        return $model->pluck('name');
    }

    public function getTotalCount(Request $request)
    {
        $model = new SocialServiceAssistance;
        if ($request->has('search_status') && !empty($request->search_status)) {
            $model = $model->where('status', $request->search_status);
        } else {
            $model = $model->where('status', '!=', SocialServiceAssistance::STATUS_FOR_VALIDATION);
            $model = $model->where('status', '!=', SocialServiceAssistance::STATUS_FOR_DELETE);
        }
        $model = $this->buildDataTableFilter(
            $model,
            $request,
            true,
            ['first_name', 'last_name', 'middle_name', 'purpose'],
            ['purpose_text' => 'purpose'],
            ['encoder.full_name', 'approver.full_name']
        );
        if ($request->has('encoder_search') && !empty($request->encoder_search)) {
            $model = $model->where('encoder_id', $request->encoder_search);
        }
        if ($request->has('approver_search') && !empty($request->approver_search)) {
            $model = $model->where('approved_by', $request->approver_search);
        }
        if ($request->has('request_type_search') && !empty($request->request_type_search)) {
            $model = $model->where('request_type_id', $request->request_type_search);
        }
        if ($request->has('releaser_search') && !empty($request->releaser_search)) {
            $model = $model->where('releaser_id', $request->releaser_search);
        }
        return $model->count();
    }

    public function getTotalCountByStatus(string $status)
    {
        return SocialServiceAssistance::where('status', $status)->count();
    }

    public function getCurrentControlNumber(Request $request)
    {
        return SocialServiceAssistance::where('request_type_id', $request->id)
            ->where('status', '!=',  'On-hold')
            ->where('status', '!=', 'Rejected')
            ->where('status', '!=', 'For-validation')
            ->where('status', '!=', 'For-delete')
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->pluck('control_number')->map(function ($control_number) {
                return str_pad(abs($control_number) + 1, self::MAX_CHAR_CONTROL_NUMBER, "0", STR_PAD_LEFT);
            });
    }

    private function checkIfRequestorExists(Request $request)
    {

        $requestor_signature = $request->requestor_last_name . $request->requestor_first_name . $request->requestor_middle_name . $request->requestor_suffix;
        $beneficiary_signature = $request->last_name . $request->first_name . $request->middle_name . $request->suffix;
        if ($requestor_signature !== $beneficiary_signature) {
            $model = SocialServiceAssistance::where('requestor_last_name', $request->requestor_last_name)
                ->where('requestor_first_name', $request->requestor_first_name)
                ->where('requestor_middle_name', $request->requestor_middle_name)
                ->where('requestor_suffix', $request->requestor_suffix)
                ->whereIn('status', ['Approved', 'Released'])
                ->whereHas('tag', fn($q) => $q->whereNotIn('name', self::EXEMPTED_REQUEST_TYPES))
                ->orderBy('approved_date', 'desc')
                ->first();
            if ($model) {
                $exisiting_requestor_signature = $model->requestor_last_name . $model->requestor_first_name . $model->requestor_middle_name . $model->requestor_suffix;
                $exisiting_beneficiary_signature = $model->last_name . $model->first_name . $model->middle_name . $model->suffix;
                if ($exisiting_requestor_signature !== $exisiting_beneficiary_signature) {
                    // Check if the release date and the date today difference is still 90 days
                    $dayDiff = Carbon::parse($model->approved_date)->diffInDays(Carbon::now());
                    if ($dayDiff <= self::REQUEST_INTERVAL_IN_DAYS) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function getSocialServicesAmountPerYear(string $year, $source = "")
    {
        $social_services = SocialServiceAssistance::selectRaw("MONTH(release_date) as release_month, SUM(amount) as grand_total_amount")
            ->whereRaw("YEAR(release_date) = ? ", [$year])
            ->when(!empty($source), fn($q) => $q->where("source", $source))
            ->groupBy(["release_month"])
            ->having("grand_total_amount", ">", 0)
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $social_service_data = [];
        $grand_total = 0;
        $social_service_data["months"] = $months;
        foreach ($months as $index => $month) {
            $month_numeric = $index + 1;
            $hasFound = false;
            foreach ($social_services as $social_service) {
                if ($month_numeric == (int) $social_service->release_month) {
                    $social_service_data["total_amount"][] = $social_service->grand_total_amount;
                    $grand_total += $social_service->grand_total_amount;
                    $hasFound = true;
                    break;
                }
            }
            if (!$hasFound)
                $social_service_data["total_amount"][] = 0;
        }
        $social_service_data["grand_total"] = $grand_total;
        return $social_service_data;
    }

    public function getReportByBrgy(Request $request): array
    {
        $model = SocialServiceAssistance::selectRaw('count(*) as count, sum(amount) as total_amount, brgy, is_voter');
        if ($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                if (!in_array($field, ['date_from', 'date_to'])) {
                    $model = $model->where($field, 'LIKE',  $value . '%');
                }
            }
            if (isset($request_arr['filter']['date_from'])) {
                if (isset($request_arr['filter']['date_to'])) {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_to'] . " 23:59:59"]);
                } else {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_from'] . " 23:59:59"]);
                }
            }
        }
        $model = $model->groupBy(['brgy', 'is_voter'])->orderBy('brgy', 'asc');
        $datas = $model->get();
        $data_arr = [];
        foreach ($datas as $data) {
            if ($data->is_voter == 1) {
                $data_arr[$data->brgy]['voter'] = number_format($data->count);
            } else {
                $data_arr[$data->brgy]['non_voter'] = number_format($data->count);
            }
            $data_arr[$data->brgy]['total_amount'] = number_format($data->total_amount);
        }
        return $data_arr;
    }

    public function getReportByRequestType(Request $request): array
    {
        $model = SocialServiceAssistance::with(['tag']);
        $model = $model->selectRaw('count(*) as count, sum(amount) as total_amount, request_type_id, is_voter');
        if ($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                if (!in_array($field, ['date_from', 'date_to'])) {
                    $model = $model->where($field, 'LIKE',  $value . '%');
                }
            }
            if (isset($request_arr['filter']['date_from'])) {
                if (isset($request_arr['filter']['date_to'])) {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_to'] . " 23:59:59"]);
                } else {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_from'] . " 23:59:59"]);
                }
            }
        }
        $model = $model->groupBy(['request_type_id', 'is_voter'])->orderBy('request_type_id', 'asc');
        $datas = $model->get();
        $data_arr = [];
        foreach ($datas as $data) {
            if ($data->is_voter == 1) {
                $data_arr[$data->tag->name]['voter'] = number_format($data->count);
            } else {
                $data_arr[$data->tag->name]['non_voter'] = number_format($data->count);
            }
            $data_arr[$data->tag->name]['total_amount'] = number_format($data->total_amount);
        }
        return $data_arr;
    }

    public function getReportByStatus(Request $request): array
    {
        $model = SocialServiceAssistance::selectRaw('count(*) as count, sum(amount) as total_amount, status, is_voter');
        if ($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                if (!in_array($field, ['date_from', 'date_to'])) {
                    $model = $model->where($field, 'LIKE',  $value . '%');
                }
            }
            if (isset($request_arr['filter']['date_from'])) {
                if (isset($request_arr['filter']['date_to'])) {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_to'] . " 23:59:59"]);
                } else {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_from'] . " 23:59:59"]);
                }
            }
        }
        $model = $model->groupBy(['status', 'is_voter'])->orderBy('status', 'asc');
        $datas = $model->get();
        $data_arr = [];
        foreach ($datas as $data) {
            if ($data->is_voter == 1) {
                $data_arr[$data->status]['voter'] = number_format($data->count);
            } else {
                $data_arr[$data->status]['non_voter'] = number_format($data->count);
            }
            $data_arr[$data->status]['total_amount'] = number_format($data->total_amount);
        }
        return $data_arr;
    }

    public function getReportData(Request $request): array
    {

        $model = SocialServiceAssistance::selectRaw('sum(amount) as total_amount')->where('status', 'Released');
        if ($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                if (!in_array($field, ['date_from', 'date_to'])) {
                    $model = $model->where($field, 'LIKE',  $value . '%');
                }
            }
            if (isset($request_arr['filter']['date_from'])) {
                if (isset($request_arr['filter']['date_to'])) {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_to'] . " 23:59:59"]);
                } else {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_from'] . " 23:59:59"]);
                }
            }
        }
        $total_amount_released = number_format($model->pluck('total_amount')->first(), 2);


        $model = new SocialServiceAssistance();
        if ($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                if (!in_array($field, ['date_from', 'date_to'])) {
                    $model = $model->where($field, 'LIKE',  $value . '%');
                }
            }
            if (isset($request_arr['filter']['date_from'])) {
                if (isset($request_arr['filter']['date_to'])) {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_to'] . " 23:59:59"]);
                } else {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_from'] . " 23:59:59"]);
                }
            }
        }
        $total_count = $model->count();


        $model = SocialServiceAssistance::selectRaw('count(*) as count, is_voter');
        if ($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                if (!in_array($field, ['date_from', 'date_to'])) {
                    $model = $model->where($field, 'LIKE',  $value . '%');
                }
            }
            if (isset($request_arr['filter']['date_from'])) {
                if (isset($request_arr['filter']['date_to'])) {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_to'] . " 23:59:59"]);
                } else {
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from'] . " 00:00:00", $request_arr['filter']['date_from'] . " 23:59:59"]);
                }
            }
        }
        $model = $model->groupBy(['is_voter'])->orderBy('is_voter', 'asc');

        $datas = $model->get();
        $number_of_non_voters_and_voters = [];
        foreach ($datas as $data) {
            if ($data->is_voter == 1) {
                $number_of_non_voters_and_voters['voter'] = $data->count;
            } else {
                $number_of_non_voters_and_voters['non-voter'] = $data->count;
            }
        }

        return [
            'number_of_non_voters_and_voters' => $number_of_non_voters_and_voters,
            'total_count' => $total_count,
            'total_amount_released' => $total_amount_released,
        ];
    }

    private function checkIfBeneficiaryExists(Request $request)
    {
        $model = SocialServiceAssistance::where('last_name', $request->last_name)
            ->where('first_name', $request->first_name)
            ->where('middle_name', $request->middle_name)
            ->where('suffix', $request->suffix)
            ->where('brgy', $request->brgy)
            ->whereIn('status', ['Approved', 'Released'])
            ->whereHas('tag', fn($q) => $q->whereNotIn('name', self::EXEMPTED_REQUEST_TYPES))
            ->orderBy('approved_date', 'desc')
            ->first();
        if ($model) {
            $dayDiff = Carbon::parse($model->approved_date)->diffInDays(Carbon::now());
            if ($dayDiff <= self::REQUEST_INTERVAL_IN_DAYS) {
                return true;
            }
        }
        return false;
    }

    private function checkIfBeneficiaryExistsInEvent(Request $request)
    {
        if(!isset($request->event_id) || empty($request->event_id)) {
            return false;
        }
        return SocialServiceAssistance::where('last_name', $request->last_name ?? "")
            ->where('first_name', $request->first_name ?? "")
            ->where('middle_name', $request->middle_name ?? "")
            ->where('suffix', $request->suffix ?? "")
            ->where('event_id', $request->event_id)
            ->exists();
    }

    public function updateStatusMultiple(Request $request)
    {
        if ($request->has('selected_ids') && $request->has('status')) {
            $toNotify = true;
            foreach ($request->selected_ids as $selected_id) {
                $model = SocialServiceAssistance::find($selected_id);

                if ($model->status === SocialServiceAssistance::STATUS_RELEASED) {
                    Log::info("Can't Update Assistance ID {$model->id} from Released to {$request->status}");
                    continue;
                }

                ///
                $control_number = $model->control_number;
                $encoder = $model->encoder;
                $previous_status = $model->status;
                $notificationMessage = "Request with Control # {$control_number} changed its status from {$previous_status} to {$request->status} by " . Auth::user()->full_name;
                ///
                $model->status = $request->status;
                if ($request->status == SocialServiceAssistance::STATUS_APPROVED) {
                    $model->approved_date = date('Y-m-d');
                    $model->approved_by = Auth::user()->id;
                } elseif ($request->status == SocialServiceAssistance::STATUS_FOR_DELETE) {
                    $toNotify = false;
                }
                $model->save();

                if ($toNotify) {
                    //Notification::send($encoder, new AppNotification('Social Service Request Status Update', $notificationMessage, $model, url('social_services')));
                    activity("Update Social Service Request")
                        ->causedBy(Auth::user())
                        ->performedOn($model)
                        ->withProperties($model)
                        ->log('Update Social Service Request Status. ' . $model->control_number . ' | Status: ' . $request->status);
                }
            }
            return 1;
        }
        return 0;
    }

    public function deleteMultiple(Request $request)
    {
        if ($request->has('selected_ids')) {
            foreach ($request->selected_ids as $selected_id) {
                $model = SocialServiceAssistance::find($selected_id);
                activity("Delete Social Service Request")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Delete Muliple Social Service Request');
                $model->delete();
            }
            return 1;
        }
        return 0;
    }

    public function generateControlNumber($request_type_id): string
    {
        $social_service = SocialServiceAssistance::where('request_type_id', $request_type_id)
            ->orderBy("control_number", "DESC") 
            ->first();

        $last_control_number= $social_service ? abs($social_service->control_number) : 0;
        $new_control_number = str_pad($last_control_number + 1, 8, "0", STR_PAD_LEFT);

        $result = SocialServiceAssistance::where('control_number', $new_control_number)
            ->where('request_type_id', $request_type_id)
            ->first();

        if ($result) {
            return $this->generateControlNumber($request_type_id);
        }
        return $new_control_number;
    }

    public function createFromAttendee($event, $attendee)
    {   

        $response = [
            "status" => 0,
            "message" => "Unexepected error in assistance creation.",
        ];

        try {

            if (
                SocialServiceAssistance::where('event_id', $event->id)
                    ->where('first_name', $attendee->first_name)
                    ->where('last_name', $attendee->last_name)
                    ->where('middle_name', $attendee->middle_name)
                    ->where('suffix', $attendee->suffix)
                    ->where('birth_date', $attendee->birth_date)
                    ->exists()
            ) {
                throw new Exception("social service already exists for this attendee");
            }

            $control_number = $this->generateControlNumber($event->request_type_id);

            $model = new SocialServiceAssistance;
            $model->request_type_id = $event->request_type_id;
            $model->purpose = $event->purpose;
            $model->amount = $event->amount;
            $model->remarks = $event->description;
            $model->control_number = $control_number;
            $model->status = SocialServiceAssistance::STATUS_RELEASED;
            $model->event_id = $event->id;

            $model->first_name = $attendee->first_name;
            $model->middle_name = $attendee->middle_name;
            $model->last_name = $attendee->last_name;
            $model->suffix = $attendee->suffix;
            $model->birth_date = $attendee->birth_date;
            $model->contact_number = $attendee->contact_number;
            $model->brgy = $attendee->brgy;
            $model->address = $attendee->address;
            $model->is_voter = $attendee->is_voter;
            $model->precinct = $attendee->precinct;

            $model->file_date = Carbon::create($attendee->created_at)->format('Y-m-d');
            $model->processed_date = Carbon::create($attendee->created_at)->format('Y-m-d');
            $model->release_date = Carbon::now()->format('Y-m-d');
            $model->encoder_id = Auth::user()->id;
            $model->approved_by = Auth::user()->id;
            $model->processed_by = Auth::user()->full_name;

            $model->received_by = Auth::user()->full_name;
            $model->referred_by = '';
            $model->organization = '';
            $model->requestor_last_name = $attendee->last_name;
            $model->requestor_first_name = $attendee->first_name;
            $model->requestor_middle_name = $attendee->middle_name;
            $model->requestor_suffix = $attendee->suffix;
            $model->requestor_relationship_to_beneficiary = '';
            $model->requestor_same_to_beneficiary = 0;
            $model->is_deceased = 0;

            activity("Create New Social Service Request from Event")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Create New Social Service Request from Event. Control # ' . $control_number);

            $model->save();

            $response["status"] = 1;
            $response["message"] = "success";

        } catch (Exception $e) {
            report($e);
            $response["status"] = 0;
            $response["message"] = $e->getMessage();
        }
        
        return $response;
    }

    public function getAssistanceHistory(array $payload, int $limit = 10, int $notId = 0)
    {
        return SocialServiceAssistance::where('last_name', $payload['last_name'])
            ->where('first_name', $payload['first_name'])
            ->where('middle_name', $payload['middle_name'])
            ->where('suffix', $payload['suffix'])
            ->where('birth_date', $payload['birth_date'])
            ->get();
    }


    public function getMemberAssistanceHistory(array $payload)
    {
        return SocialServiceAssistance::where('last_name', $payload['last_name'])
            ->where('first_name', $payload['first_name'])
            ->where('middle_name', $payload['middle_name'])
            ->where('suffix', $payload['suffix'])
            ->where('birth_date', $payload['birth_date'])
            ->orderBy('file_date', 'desc')
            ->get();
    }
}
