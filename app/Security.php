<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Security extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'security';

    protected $fillable = ["user_id","password","status"];

    public function owner(){
    	return $this->belongsTo('App\User','user_id')->withDefault();
    }
}