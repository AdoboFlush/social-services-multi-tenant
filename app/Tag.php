<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'status',
        'custom_field',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
    
    protected $appends = ['parent_name'];

    public function getParentNameAttribute()
    {   
        if($this->attributes['parent_id'] > 0){
            $model = DB::table($this->getTable())->where('id', $this->attributes['parent_id'])->first();
            if($model){
                return $model->name;
            }
        }
        return '';
    }

}
