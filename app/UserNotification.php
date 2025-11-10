<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'user_notifications';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'notification_id',
        'status',
        'read'
    ];   

    public function user(){
		return $this->belongsTo('App\User','user_id');
	}
	
	public function notification(){
		return $this->belongsTo('App\Notification','notification_id');
	}    
}