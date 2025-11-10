<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tag;


class SocialServiceAssistance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'control_number',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'contact_number',
        'brgy',
        'address',
        'organization',
        'purpose',
        'remarks',
        'referred_by',
        'received_by',
        'processed_by',
        'file_date',
        'processed_date',
        'release_date',
        'amount',
        'approved_by',
        'encoder',
        'status',
        'event_id',
        'source',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
    protected $appends = ['purpose_text', 'request_type', 'full_name', 'requestor_full_name', 'event_name'];

    public const STATUS_APPROVED = "Approved";
    public const STATUS_REJECTED = "Rejected";
    public const STATUS_ON_HOLD = "On-hold";
    public const STATUS_PENDING = "Pending";
    public const STATUS_FOR_VALIDATION = "For-validation";
    public const STATUS_RELEASED = "Released";
    public const STATUS_FOR_DELETE = "For-delete";

    public const SOURCE_ARJO = "ARJO";
    public const SOURCE_GAB = "GAB";
    
    public function encoder()
    {
        return $this->belongsTo('App\User', 'encoder_id')->withDefault();
    }

    public function approver()
    {
        return $this->belongsTo('App\User', 'approved_by')->withDefault();
    }

    public function releaser()
    {
        return $this->belongsTo('App\User', 'releaser_id')->withDefault();
    }

    public function tag()
    {
        return $this->belongsTo('App\Tag', 'request_type_id')->withDefault();
    }

    public function getPurposeTextAttribute()
    {
        return !empty($this->attributes['purpose']) ? implode(", ", json_decode($this->attributes['purpose'], true)) : "";
    }

    public function getRequestTypeAttribute()
    {
        return Tag::where('id',$this->attributes['request_type_id'])->pluck('name')->first();
    }

    public function getFullNameAttribute()
    {
        return "{$this->last_name}, {$this->first_name} {$this->middle_name} {$this->suffix}";
    }

    public function getRequestorFullNameAttribute()
    {
        return "{$this->requestor_last_name}, {$this->requestor_first_name} {$this->requestor_middle_name} {$this->requestor_suffix}";
    }

    public function getEventNameAttribute()
    {
        return intval($this->event_id) > 0 ? $this->event->name : '';
    }

    public function event()
    {
        return $this->belongsTo('App\Event', 'event_id')->withDefault();
    }

}
