<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwoFaCode extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'two_fa_codes';

    public function user(){
    	return $this->belongsTo('App\User','user_id');
    }

}