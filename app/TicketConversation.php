<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketConversation extends Model
{
    protected $fillable = [
        'ticket_id',
        'sender_id',
        'department',
        'message',
        'attachment',
        'is_seen',
    ];

    public function sender(){
        return $this->belongsTo('\App\User','sender_id');
    }
}