<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PollEntry extends Model
{
    protected $fillable = ['poll_election_candidate_id', 'poll_election_watcher_id', 'poll_election_id', 'votes', 'remarks', 'is_active', 'clustered_precinct_votes', 'status'];

    protected $appends = ["clustered_vote_entry_count"];

    public function pollElectionCandidate()
    {
        return $this->belongsTo(PollElectionCandidate::class);
    }

    public function pollElectionWatcher()
    {
        return $this->belongsTo(PollElectionWatcher::class);
    }

    public function pollElection()
    {
        return $this->belongsTo(PollElection::class);
    }

    public function getClusteredVoteEntryCountAttribute()
    {
        return !empty($this->clustered_precinct_votes) 
            ? count(json_decode($this->clustered_precinct_votes, true)) : 0;
    }
}
