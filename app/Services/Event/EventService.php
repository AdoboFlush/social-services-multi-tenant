<?php

namespace App\Services\Event;

use App\Attendee;
use App\Event;
use App\Services\BaseService;
use App\Services\SocialServiceAssistance\SocialServiceAssistanceFacade;
use App\SocialServiceAssistance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventService extends BaseService
{
    public function __construct()
    { 
    }

    public function store(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'event_name' => 'required',
                'hosted_by' => 'required',
                'minimum_attendees' => 'required',
                'maximum_attendees' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'description' => 'required',
                'venue' => 'required',
                'color' => 'required',
            ]);

            if ($validator->fails()) {
                $message = _lang(($validator->errors()->first()));
                return back()->withInput()->with('error', $message);
            }

            $request->start_time = Carbon::parse($request->start_time)->format("H:i:s");
            $request->end_time = Carbon::parse($request->end_time)->format("H:i:s");

            $model = new Event;
            $model->name = !empty($request->event_name) ? $request->event_name : '';
            $model->hosted_by = !empty($request->hosted_by) ? $request->hosted_by : '';
            $model->minimum_attendees = !empty($request->minimum_attendees) ? $request->minimum_attendees : 0;
            $model->maximum_attendees = !empty($request->maximum_attendees) ? $request->maximum_attendees : 0;
            $model->start_at = !empty($request->start_time) && !empty($request->start_date) ? $request->start_date . " " . $request->start_time : null;
            $model->end_at = !empty($request->end_time) && !empty($request->end_date) ? $request->end_date . " " . $request->end_time : null;
            $model->venue = !empty($request->venue) ? $request->venue : '';
            $model->description = !empty($request->description) ? $request->description : '';
            $model->color = !empty($request->color) ? $request->color : '';
            $model->active = 1;
            $model->request_type_id =  !empty($request->request_type_id) ? $request->request_type_id : 0;
            $model->purpose =  !empty($request->purpose) ? json_encode($request->purpose) : '';
            $model->amount =  !empty($request->amount) ? $request->amount : 0;
            $affected_rows = $model->save();

            activity("Create Event")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Create Event : ' . $model->event_name);
            return back()->with('success', 'Record has been inserted!');
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                report($e);
                $message = "Duplicate Entry.";
            }
        } catch (Exception $e) {
            report($e);
            $message = $e->getMessage();
        }

        return back()->with('error', 'Record insert failed - ' . $message);
    }

    public function storeAttendee(Request $request)
    {

        $message = "Unexpected Error Occurred";
        
        $attendee = Attendee::where('event_id', $request->event_id)
        ->where('first_name', $request->first_name)
        ->where('last_name', $request->last_name)
        ->where('middle_name', !empty($request->middle_name) ? $request->middle_name : '')
        ->where('suffix', !empty($request->suffix) ? $request->suffix : '')
        ->where('birth_date', $request->birth_date)
        ->first();

        if ($attendee){
            return back()->with('error', 'Duplicate entry');
        }
        
        try {

            $model = new Attendee;
            $model->event_id = $request->event_id;
            $model->first_name = $request->first_name;
            $model->last_name = $request->last_name;
            $model->middle_name = !empty($request->middle_name) ? $request->middle_name : '';
            $model->suffix = !empty($request->suffix) ? $request->suffix : '';
            $model->birth_date = !empty($request->birth_date) ? $request->birth_date : '';
            $model->gender = !empty($request->gender) ? $request->gender : '';
            $model->precinct = !empty($request->precinct) ? $request->precinct : '';
            $model->address = !empty($request->address) ? $request->address : '';
            $model->brgy = !empty($request->brgy) ? $request->brgy : '';
            $model->alliance = !empty($request->alliance) ? $request->alliance : '';
            $model->affiliation = !empty($request->affiliation) ? $request->affiliation : '';
            $model->civil_status = !empty($request->civil_status) ? $request->civil_status : '';
            $model->beneficiary = !empty($request->beneficiary) ? $request->beneficiary : '';
            $model->religion = !empty($request->religion) ? $request->religion : '';
            $model->contact_number = !empty($request->contact_number) ? $request->contact_number : '';
            $model->remarks = !empty($request->remarks) ? $request->remarks : '';
            $model->is_voter = $request->has('is_voter') ? 1 : 0;
            $affected_rows = $model->save();

            activity("Create Attendee")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Create Attendee : ' . $model->last_name . ', ' . $model->first_name . ' ' . $model->middle_name . ' ' . $model->suffix);
            
            return back()->with('success', 'Record has been inserted!');
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                report($e);
                $message = "Duplicate Entry.";
            }
        } catch (Exception $e) {
            report($e);
            $message = $e->getMessage();
        }

        return back()->with('error', 'Record insert failed - ' . $message);
    }

    public function update(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'event_name' => 'required',
                'hosted_by' => 'required',
                'minimum_attendees' => 'required',
                'maximum_attendees' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'description' => 'required',
                'venue' => 'required',
                'update_id' => 'required',
            ]);

            if ($validator->fails()) {
                $message = _lang(($validator->errors()->first()));
                return back()->withInput()->with('error', $message);
            }

            $model = Event::find($request->update_id);
            if (!$model) {
                return back()->with('error', 'Record not found. Update failed');
            }

            $model->name = !empty($request->event_name) ? $request->event_name : '';
            $model->hosted_by = !empty($request->hosted_by) ? $request->hosted_by : '';
            $model->minimum_attendees = !empty($request->minimum_attendees) ? $request->minimum_attendees : 0;
            $model->maximum_attendees = !empty($request->maximum_attendees) ? $request->maximum_attendees : 0;
            $model->start_at = !empty($request->start_time) && !empty($request->start_date) ? $request->start_date . " " . $request->start_time : null;
            $model->end_at = !empty($request->end_time) && !empty($request->end_date) ? $request->end_date . " " . $request->end_time : null;
            $model->venue = !empty($request->venue) ? $request->venue : '';
            $model->description = !empty($request->description) ? $request->description : '';
            $model->active = $request->has('is_active') ? 1 : 0;
            $model->color = !empty($request->color) ? $request->color : '';
            $model->request_type_id =  !empty($request->request_type_id) ? $request->request_type_id : 0;
            $model->purpose =  !empty($request->purpose) ? json_encode($request->purpose) : '';
            $model->amount =  !empty($request->amount) ? $request->amount : 0;
            $affected_rows = $model->save();

            activity("Update Event")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Update Event : ' . $model->name);
            return back()->with('success', 'Record has been updated!');
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                report($e);
                $message = "Duplicate Entry";
            }
        } catch (Exception $e) {
            report($e);
            $message = $e->getMessage();
        }

        return back()->with('error', 'Record insert failed - ' . $message);
    }

    public function updateAttendee(Request $request)
    {

        try {

            $model = Attendee::find($request->update_id);
            if (!$model) {
                return back()->with('error', 'Record not found. Update failed');
            }
            $model->first_name = $request->first_name;
            $model->last_name = $request->last_name;
            $model->middle_name = !empty($request->middle_name) ? $request->middle_name : '';
            $model->suffix = !empty($request->suffix) ? $request->suffix : '';
            $model->birth_date = !empty($request->birth_date) ? $request->birth_date : '';
            $model->gender = !empty($request->gender) ? $request->gender : '';
            $model->precinct = !empty($request->precinct) ? $request->precinct : '';
            $model->address = !empty($request->address) ? $request->address : '';
            $model->brgy = !empty($request->brgy) ? $request->brgy : '';
            $model->alliance = !empty($request->alliance) ? $request->alliance : '';
            $model->affiliation = !empty($request->affiliation) ? $request->affiliation : '';
            $model->civil_status = !empty($request->civil_status) ? $request->civil_status : '';
            $model->beneficiary = !empty($request->beneficiary) ? $request->beneficiary : '';
            $model->religion = !empty($request->religion) ? $request->religion : '';
            $model->contact_number = !empty($request->contact_number) ? $request->contact_number : '';
            $model->remarks = !empty($request->remarks) ? $request->remarks : '';
            $model->is_voter = $request->has('is_voter') ? 1 : 0;
            $affected_rows = $model->save();

            activity("Update Attendee")
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($model)
                ->log('Update Attendee : ' . $model->last_name . ', ' . $model->first_name . ' ' . $model->middle_name . ' ' . $model->suffix);
            return back()->with('success', 'Record has been updated!');
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                report($e);
                $message = "Duplicate Entry";
            }
        } catch (Exception $e) {
            report($e);
            $message = $e->getMessage();
        }

        return back()->with('error', 'Record insert failed - ' . $message);
    }

    public function getAll(Request $request)
    {
        $model = new Event;
        if ($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                if (empty($value)) continue;
                if ($field == "start_date") {
                    $model = $model->whereRaw("DATE(start_at) >= ?", [$value]);
                } else {
                    $model = $model->where($field, 'LIKE',  $value . '%');
                }
            }
        }
        $model = $this->buildDataTableFilter($model, $request);
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        return [
            'data' => $model->get(),
            'total' => $total_count
        ];
    }

    public function getAllAttendees(Request $request)
    {
        $model = new Attendee();
        if ($request->has('filter')) {
            $request_arr = $request->all();
            foreach ($request_arr['filter'] as $field => $value) {
                $model = $model->where($field, 'LIKE',  $value . '%');
            }
        }
        $model = $this->buildDataTableFilter($model, $request);
        $model = $model->where("event_id", $request->id);
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        return [
            'data' => $model->get(),
            'total' => $total_count
        ];
    }

    public function deleteMultiple(Request $request)
    {
        if ($request->has('selected_ids')) {
            foreach ($request->selected_ids as $selected_id) {
                $model = Event::find($selected_id);
                activity("Delete Event")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Delete Event ID : ' . $selected_id);
                $model->delete();
            }
            return 1;
        }
        return 0;
    }

    public function deleteMultipleAttendee(Request $request)
    {
        $response = [
            "status" => 0,
            "message" => "Unexpected Error",
        ];
        try {
            DB::beginTransaction();
            if ($request->has('selected_ids')) {
                foreach ($request->selected_ids as $selected_id) {
                    $model = Attendee::find($selected_id);
                    if($model->social_service_assistance_status !== SocialServiceAssistance::STATUS_PENDING) {
                        throw new Exception("Cannot delete an attendee with released social service for this event");
                    }
                    activity("Delete Attendee")
                        ->causedBy(Auth::user())
                        ->performedOn($model)
                        ->withProperties($model)
                        ->log('Delete Attendee ID : ' . $selected_id);
                    $model->delete();
                }
            }

            $response["status"] = 1;
            $response["message"] = "success";
            DB::commit();
        } catch (Exception $e) {
            Log::info($e->getMessage());
            $response["message"] = $e->getMessage();
            DB::rollBack();
        }
        return $response;
    }

    
    public function getMemberEvents(array $payload)
    {
        $date_today = now()->format("Y-m-d");
        $past = collect();
        $ongoing = collect();
        $upcoming = collect();

        $attendee_of_events = Attendee::with('event')->where('last_name', $payload['last_name'])
        ->where('first_name', $payload['first_name'])
        ->where('middle_name', $payload['middle_name'])
        ->where('suffix', $payload['suffix'])
        ->where('birth_date', $payload['birth_date'])
        ->where('gender', $payload['gender'])
        ->get();

        foreach($attendee_of_events as $attendee_of_event){
            $event_start = Carbon::create($attendee_of_event->event->start_at)->format("Y-m-d");
            $event_end = Carbon::create($attendee_of_event->event->end_at)->format("Y-m-d");
            if ( $event_end < $date_today){
                $past->add([
                    "name" =>  $attendee_of_event->event->name,
                    "start_at" => Carbon::create($attendee_of_event->event->start_at)->format("F d, Y H:i"),
                    "end_at" => Carbon::create($attendee_of_event->event->end_at)->format("F d, Y H:i"),
                    "venue" => $attendee_of_event->event->venue,
                    "description" => $attendee_of_event->event->description,
                    ]
                );
            }else if ($event_start > $date_today ){
                $upcoming->add([
                    "name" =>  $attendee_of_event->event->name,
                    "start_at" => Carbon::create($attendee_of_event->event->start_at)->format("F d, Y H:i"),
                    "end_at" => Carbon::create($attendee_of_event->event->end_at)->format("F d, Y H:i"),
                    "venue" => $attendee_of_event->event->venue,
                    "description" => $attendee_of_event->event->description,
                ]);
            }
            else{
                $ongoing->add([
                    "name" =>  $attendee_of_event->event->name,
                    "start_at" => Carbon::create($attendee_of_event->event->start_at)->format("F d, Y H:i"),
                    "end_at" => Carbon::create($attendee_of_event->event->end_at)->format("F d, Y H:i"),
                    "venue" => $attendee_of_event->event->venue,
                    "description" => $attendee_of_event->event->description,
                ]);
            } 
        }
        return ["past" => $past, "ongoing" => $ongoing, "upcoming" => $upcoming];
    }
}