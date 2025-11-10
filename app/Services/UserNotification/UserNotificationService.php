<?php

namespace App\Services\UserNotification;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Services\BaseService;

use Illuminate\Http\Request;
use App\Http\Requests\CreateNotificationRequest;

use App\Repositories\Notification\NotificationInterface;
use App\Repositories\UserNotification\UserNotificationInterface;
use App\Repositories\User\UserInterface;

use Auth;
use DB;

class UserNotificationService extends BaseService
{

    protected $notificationInterface;
    protected $userNotificationInterface;
    protected $userInterface;

    public function __construct(
        NotificationInterface $notificationInterface,
        UserNotificationInterface $userNotificationInterface,
        UserInterface $userInterface
   ) {
        $this->notificationInterface = $notificationInterface;
        $this->userNotificationInterface = $userNotificationInterface;
        $this->userInterface = $userInterface;
    }    

    public function updateToPublish()
    {
        try {
            Log::info('PUBLISH NOW: ' . Carbon::now()->format('Y-M-d H:i A'));
            $notifs = $this->notificationInterface->getNotificationNow();
            Log::info('NOTIFICATIONS: ' . json_encode($notifs ));
            $users = $this->userInterface->getAllUsers();

            foreach ($notifs as $notif) {
                foreach($users as $user) {
                    $payload['user_id'] = $user->id;
                    $payload['notification_id'] = $notif->id;
                    $payload['status'] = 1;                    

                    $notif_create = $this->userNotificationInterface->create($payload);
                }
            }     
            return response()->json(['success' => true]);
        } catch (Exception $e) {            
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::error('USER NOTIFICATION ERROR: ' . $message);
            return response()->json(['success' => false, 'message' => $message]);
        }
    }
}
