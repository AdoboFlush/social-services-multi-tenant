<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollCandidate extends Model
{
    protected $fillable = ['name', 'image', 'remarks', 'is_active'];

    public function pollEntries()
    {
        return $this->hasMany(PollEntry::class);
    }

    public function pollElectionCandidates()
    {
        return $this->hasMany(PollElectionCandidate::class);
    }

    public function electionCandidates()
    {
        return $this->hasMany(PollElectionCandidate::class);
    }
}
