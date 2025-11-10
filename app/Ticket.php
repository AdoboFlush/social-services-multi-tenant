<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'department',
        'operator_id',
        'subject',
        'priority',
        'status',
        'tag',
        'updated_at'
    ];

    public function user(){
        return $this->belongsTo('\App\User','user_id');
    }

    public function operator(){
        return $this->belongsTo('\App\User','operator_id');
    }

    public function conversations(){
        return $this->hasMany('App\TicketConversation','ticket_id')->orderBy('created_at','desc');
    }

    public function user_information(){
        return $this->belongsTo('\App\UserInformation','user_id','user_id');
    }
}