<?php

namespace App;

use App\Traits\ActivityLog\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use LogsActivity;

    protected $table = 'user_informations';

    protected $fillable = [
        'account_closed_at',
        'account_declared_dormant_at',
        'account_verified_at',
        'account_suspended_at',
        'address',
        'city',
        'country_of_residence',
        'date_of_birth',
        'language',
        'last_login_at',
        'remarks',
        'state',
        'zip',
        'website_url'
    ];

    protected static $logName = 'User Account Detail';

    protected static $ignoreChangedAttributes = ['created_at', 'updated_at'];

    protected static $logAttributes = ["*"];

    protected static $logOnlyDirty = true;

    protected static $recordEvents = ['updated', 'deleted'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id')->withDefault();
    }

    public function logDetails()
    {
        return $this->user->first_name . " " . $this->user->last_name . " " . $this->user->account_number;
    }
}
