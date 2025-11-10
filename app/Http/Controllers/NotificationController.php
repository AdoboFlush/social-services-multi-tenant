<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\Notification\NotificationFacade;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
	public function __construct(NotificationFacade $notificationFacade)
    {        
        $this->notificationFacade = $notificationFacade;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('backend.notification.list');
    }

    public function markAllAsRead(Request $request){
        return $this->notificationFacade::markAllAsRead();
    }
       

}
