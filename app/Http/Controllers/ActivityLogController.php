<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Services\ActivityLog\ActivityLogFacade;
use Illuminate\Http\Request;
use App\User;

class ActivityLogController extends Controller
{

    protected $activityLogFacade;

    public function __construct(ActivityLogFacade $activityLogFacade)
    {
        $this->activityLogFacade = $activityLogFacade;
    }

    public function delete($days = 7)
    {
        return $this->activityLogFacade::delete($days);
    }

    public function indexActivity(Request $request)
    {
        if($request->ajax()){
            $activities = $this->activityLogFacade::getAll($request);
            $total = $this->activityLogFacade::getTotalCount($request);
            return response()->json([
                'data'=> $activities,
                'recordsTotal' =>  $total,
                'recordsFiltered' =>  $total,
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }
        return response('1');
    }

    public function indexAllActivity(Request $request)
    {
        if($request->ajax()){
            $activities = $this->activityLogFacade::getAll($request, true);
            $total = $this->activityLogFacade::getTotalCount($request, true);
            return response()->json([
                'data'=> $activities,
                'recordsTotal' =>  $total,
                'recordsFiltered' =>  $total,
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{
            return view('backend.administration.activity_log.list');
        }
    }

    public function indexTaggingActivity(Request $request)
    {
        if($request->ajax()){
            $activities = $this->activityLogFacade::getAll($request, false, true);
            $total = $this->activityLogFacade::getTotalCount($request, false, true);
            return response()->json([
                'data'=> $activities,
                'recordsTotal' =>  $total,
                'recordsFiltered' =>  $total,
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{

            $taggers = User::select('id', 'first_name', 'last_name')->where('user_type', 'tagger')->get();

            return view('backend.administration.activity_log.tagging', compact('taggers'));
        }
    }

    public function show(Request $request)
    {
        $data = $this->activityLogFacade::getById($request)->toArray();
        return view('backend.administration.activity_log.modal.show', compact('data'));
    }

    
}
