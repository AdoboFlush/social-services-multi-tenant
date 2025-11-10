<?php

namespace App\Services\Notification;
use App\Services\BaseService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationService extends BaseService
{
    
    public const MINIMUM_SENIOR_CITIZEN_AGE = 60;
    private $allSearchFields = ['brgy','civil_status','full_name','religion', 'alliance', 'affiliation'];
    public function __construct()
    {
        
    }

    public function getById(Request $request)
    {   
        return Notification::find($request->id);
    }

    public function getAll(Request $request)
    {   
        $model = new Notification;
        $model = $this->buildDataTableFilter($model, $request, true, $this->allSearchFields);
        $model = $this->buildModelQueryDataTable($model, $request);
        return $model->get();
    }

    public function getTotalCount(Request $request)
    {   
        $model = new Notification;
        $model = $this->buildDataTableFilter($model, $request, true, $this->allSearchFields);
        return $model->get()->count();
    }

    public function markAllAsRead()
    {
        try{
            Auth::user()->unreadNotifications()->update(['read_at' => now()]);
            return redirect('notifications')->with('success', 'All notifications are marked as read.');
        }catch(Exception $e){
            report($e);
            return redirect('notifications')->with('error', 'Error on marking your notifications as read. Please try again.');
        }
    }

}