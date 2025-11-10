<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class VoterTagDetail extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'first_name', 
        'middle_name', 
        'last_name', 
        'suffix',
        'full_name',
        'brgy',
        'address',
        'birth_date',
        'gender',
        'precinct',
        'alliance' ,
        'affiliation',
        'contact_number',
        'civil_status',
        'religion',
        'last_update_by',
        'voter_id',
        'alliance_subgroup',
        'alliance_1',
        'alliance_1_subgroup',
        'affiliation_subgroup',
        'affiliation_1',
        'affiliation_1_subgroup',
        'sectoral',
        'sectoral_1',
        'sectoral_subgroup',
        'sectoral_1_subgroup',
        'organization',
        'is_deceased',
        'remarks',
        'party_list',
        'party_list_1',
        'position',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'last_update_by');
    }

    public function voter()
    {
        return $this->belongsTo(Voter::class, 'voter_id');
    }

    public function assistances() : HasMany
    {
        return $this->hasMany(VoterHasAssistance::class, "voter_tag_detail_id");
    }

    public function claimEventAssistance(AssistanceEvent $assistance_event)
    {
        VoterHasAssistance::create([
            "assistance_event_id" => $assistance_event->id,
            "voter_tag_detail_id" => $this->id,
            "user_id" => auth()->user()->id,
        ]);
        return $this;
    }
}
