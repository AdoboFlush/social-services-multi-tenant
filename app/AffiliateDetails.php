<?php

namespace App;

use App\Traits\ActivityLog\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateDetails extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        "code",
        "user_id",
        "parent_code",
        "integration_url",
        "sid",
        "referral_switch",
        "kyc_privilege_switch"
    ];

    public function user(){
        return $this->belongsTo('\App\User','user_id');
    }

    public function members(){
        return $this->hasMany('\App\AffiliateDetails','parent_code','code');
    }

    public function parent(){
        return $this->belongsTo('\App\AffiliateDetails','parent_code','code');
    }

    public function logDetails($event = null)
    {
        if($event == "deleted" || $event == "updated") {
            return $this->code;
        }
        return $this->user->first_name . " " . $this->user->last_name . " " . $this->user->account_number;
    }

    protected static $logName = 'Affiliates';

    protected static $logAttributes = [
        "code",
        "parent_code"
    ];
}