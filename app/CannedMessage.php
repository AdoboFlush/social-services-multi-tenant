<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CannedMessage extends Model
{
    protected $fillable = [
        'name',
        'language',
        'message',
        'internal_note',
        'status',
        'created_by',
        'updated_by',
    ];

    public function creator(){
        return $this->belongsTo('\App\User','created_by');
    }

    public function editor(){
        return $this->belongsTo('\App\User','updated_by');
    }
}