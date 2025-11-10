<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollElectionCandidate extends Model
{

    protected $fillable = ['poll_election_id', 'poll_candidate_id', 'position', 'party', 'is_active', 'party_list','national_party'];
    public const POSITIONS = [
        'Congressman',
        'Mayor',
        'Vice Mayor',
        'Councilor',
        'Party-list Representative',
        'Local Executive',
        'Board of Members',
        'Senator',
        'Governor',
        'Vice Governor',
    ];

    public const PARTIES = [
        'N/A',
        'Aksyon Agad',
        'Independent',
    ];

    public const NATIONAL_PARTIES = [
        'N/A',
        'PDP-Laban',
        'Nacionalista',
        'Liberal',
        'Lakas CMD',
        'UNA',
    ];

    public const PARTYLISTS = [
        'N/A',
        'ASAP',
        'IPATUPAD',
        'BAGONG HENERASYON',
        'SBP',
    ];
    
    public function pollEntries()
    {
        return $this->hasMany(PollEntry::class);
    }

    public function pollCandidate()
    {
        return $this->belongsTo(PollCandidate::class);
    }

    public function pollElection()
    {
        return $this->belongsTo(PollElection::class);
    }

}
