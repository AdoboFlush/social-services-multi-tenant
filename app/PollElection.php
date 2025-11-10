<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollElection extends Model
{
    protected $fillable = ['name', 'election_date', 'type', 'remarks', 'is_active', 'status'];

    public const STATUS_PENDING = 'pending';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';

    public function pollEntries()
    {
        return $this->hasMany(PollEntry::class);
    }

    public function pollElectionCandidates()
    {
        return $this->hasMany(PollElectionCandidate::class);
    }

    public function pollElectionWatchers()
    {
        return $this->hasMany(PollElectionWatcher::class);
    }

    public function electionCandidates()
    {
        return $this->hasMany(PollElectionCandidate::class);
    }
}
