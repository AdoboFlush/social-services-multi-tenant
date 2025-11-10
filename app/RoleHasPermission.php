<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    public $timestamps = false;
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'permission_id',
        'role_id',
    ];

    public function Role()
    {
        return $this->belongsTo('App\Role', 'role_id');
    }

    public function Permissions()
    {
        return $this->belongsTo('App\Permission', 'permission_id');
    }
}
