<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'hosted_by',
        'minimum_attendees',
        'maximum_attendees',
        'start_at',
        'end_at',
        'request_type_id',
        'purpose',
        'amount',
        'color',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
    protected $appends = ['purpose_text', 'request_type'];

    public function attendees()
    {
        return $this->hasMany('App\Attendee', 'event_id');
    }

    public function socialServiceAssistances()
    {
        return $this->hasMany('App\SocialServiceAssistance', 'event_id');
    }

    public function getRequestTypeAttribute()
    {
        return isset($this->attributes['request_type_id']) && $this->attributes['request_type_id'] ? Tag::where('id',$this->attributes['request_type_id'])->pluck('name')->first() : '';
    }

    public function getPurposeTextAttribute()
    {
        return isset($this->attributes['purpose']) && $this->attributes['purpose'] ? implode(", ", json_decode($this->attributes['purpose'], true)) : '';
    }
}
