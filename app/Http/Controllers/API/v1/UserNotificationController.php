<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\UserNotification\UserNotificationFacade;

class UserNotificationController extends Controller
{
	public function __construct(UserNotificationFacade $userNotification)
    {        
        $this->userNotification = $userNotification;
    }

    public function updateToPublish()
    {
        return $this->userNotification::updateToPublish();
    }    
    
}
