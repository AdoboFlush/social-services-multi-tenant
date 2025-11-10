<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class IdRequest extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
        'type',
        'member_id',
        'template_id',
        'status',
        'profile_pic',
        'signature',
        'remarks',
        'last_downloaded_at',
        'downloaded_by',
        'download_count',
    ];

    protected $appends = [
        'id_qr_value',
    ];
    
    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = [];

    public function member()
    {
        return $this->belongsTo('App\Member', 'member_id')->withDefault();
    }

    public function downloader()
    {
        return $this->belongsTo('App\User', 'downloaded_by')->withDefault();
    }

    public function template()
    {
        return $this->belongsTo('App\Template', 'template_id')->withDefault();
    }

    public function getIDQRValueAttribute()
    {
        return Crypt::encryptString($this->id_number);
    }

}
