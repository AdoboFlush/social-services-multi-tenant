<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLog\LogsActivity;

class Permission extends Model
{
    use LogsActivity;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'id',
        'name',
        'guard_name',
    ];

    protected static $logName = 'Permission';

    protected static $ignoreChangedAttributes = ['created_at','updated_at'];

    protected static $logAttributes = ["name"];

    protected static $logOnlyDirty = true;

}
