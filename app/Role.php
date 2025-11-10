<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLog\LogsActivity;

class Role extends Model
{
    use LogsActivity;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'name',
        'guard_name',
    ];

    protected static $logName = 'Roles';

    protected static $ignoreChangedAttributes = ['created_at','updated_at'];

    protected static $logAttributes = ["name"];

    protected static $logOnlyDirty = true;

    public function permissions()
    {
        return $this->belongsToMany('App\Permission','role_has_permissions');
    }
}
