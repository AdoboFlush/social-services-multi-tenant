<?php

namespace App\Services\ActivityLog;

use App\Repositories\ActivityLog\ActivityLogInterface;
use App\Repositories\User\UserInterface;
use App\Services\BaseService;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Auth;

class ActivityLogService extends BaseService
{
    const REQUEST_PER_PAGE = 10;
    public $activityLogInterface;
    public $userInterface;

    public function __construct(ActivityLogInterface $activityLogInterface,UserInterface $userInterface) {
        $this->activityLogInterface = $activityLogInterface;
        $this->userInterface = $userInterface;
    }

    public function getById(Request $request)
    {   
        return Activity::with(['causer'])->find($request->id);
    }

    public function getAll(Request $request, bool $all = false, bool $tagger = false)
    {   
        $model = new Activity;
        $model = $model->with(['causer']);

        if(!$all){
            $request_arr = $request->all();

            if (isset($request_arr['filter']['causer_id'])){
                $model = $model->where('causer_id', $request_arr['filter']['causer_id']);
            }else if ($tagger){
                $taggers = User::where("user_type", "tagger")->select('id')->pluck("id");
                $model = $model->whereIn('causer_id', $taggers);    
            }else{
                $model = $model->where('causer_id', Auth::user()->id);
            }
        }

        if($request->has('filter')){

            $request_arr = $request->all();

            foreach($request_arr['filter'] as $field => $value){
                if(!in_array($field, ['date_from', 'date_to', 'causer_id'])){
                    $model = $model->where($field, 'LIKE', $value.'%');
                }
            }

            if(isset($request_arr['filter']['date_from'])){
                if(isset($request_arr['filter']['date_to'])){
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from']." 00:00:00", $request_arr['filter']['date_to']." 23:59:59"]);
                }else{
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from']." 00:00:00", $request_arr['filter']['date_from']." 23:59:59"]);
                }
            }
    
        }
    
        $model = $this->buildDataTableFilter($model, $request);
        $model = $model->orderBy('created_at', 'desc');
        $model = $this->buildModelQueryDataTable($model, $request);
        return $model->get();
    }

    public function getTotalCount(Request $request, bool $all = false, bool $tagger = false)
    {   
        $model = new Activity;
        
        if(!$all){
            $request_arr = $request->all();

            if (isset($request_arr['filter']['causer_id'])){
                $model = $model->where('causer_id', $request_arr['filter']['causer_id']);
            }else if ($tagger){
                $taggers = User::where("user_type", "tagger")->select('id')->pluck("id");
                $model = $model->whereIn('causer_id', $taggers);    
            }else{
                $model = $model->where('causer_id', Auth::user()->id);
            }
        }

        if($request->has('filter')){

            $request_arr = $request->all();

            foreach($request_arr['filter'] as $field => $value){
                if(!in_array($field, ['date_from', 'date_to', 'causer_id'])){
                    $model = $model->where($field, 'LIKE',  $value.'%');
                }
            }

            if(isset($request_arr['filter']['date_from'])){
                if(isset($request_arr['filter']['date_to'])){
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from']." 00:00:00", $request_arr['filter']['date_to']." 23:59:59"]);
                }else{
                    $model = $model->whereBetween('created_at', [$request_arr['filter']['date_from']." 00:00:00", $request_arr['filter']['date_from']." 23:59:59"]);
                }
            }
        }
        $model = $this->buildDataTableFilter($model, $request);
        return $model->count();
    }

    public function delete($days){
        activity('Activity Log')
            ->causedBy(Auth::user())
            ->log('Deleted logs past '.$days.' days');
        Artisan::call('activitylog:clean',['--days' => $days]);
        return back()->with('success','Successfully deleted logs past '.$days.' days');
    }


}
