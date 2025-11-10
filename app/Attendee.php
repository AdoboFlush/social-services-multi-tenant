<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'brgy',
        'address',
        'birth_date',
        'gender',
        'precinct',
        'alliance',
        'affiliation',
        'civil_status',
        'beneficiary',
        'religion',
        'contact_number',
        'remarks',
        'is_voter',
        'event_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
    protected $appends = ["full_name", "social_service_assistance_status"];

    public function event()
    {
        return $this->belongsTo('App\Event', 'event_id')->withDefault();
    }

    public function getSocialServiceAssistanceStatusAttribute()
    {

        $social_service_assistance_status = 'N/A';

        if ($this->event->request_type_id) {
            $social_service_assistance = SocialServiceAssistance::where('event_id', $this->event->id)
                ->where('first_name', $this->first_name)
                ->where('last_name', $this->last_name)
                ->where('middle_name', $this->middle_name)
                ->where('birth_date', $this->birth_date)
                ->first();

            $social_service_assistance_status =  $social_service_assistance ? SocialServiceAssistance::STATUS_RELEASED : SocialServiceAssistance::STATUS_PENDING;
        }

        return $social_service_assistance_status;
    }

    public function getFullNameAttribute()
    {
        if (!empty($this->suffix)) {
            return $this->last_name. ", " . $this->first_name . " " . $this->suffix . " " . $this->middle_name;
        } else {
            return $this->last_name . ", " . $this->first_name . " " . $this->middle_name;
        }
    }
}
