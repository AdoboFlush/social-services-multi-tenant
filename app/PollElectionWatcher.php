<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollElectionWatcher extends Model
{
    protected $fillable = ["poll_watcher_id", "poll_election_id"];

    public function pollWatcher()
    {
        return $this->belongsTo(PollWatcher::class);
    }

    public function pollElection()
    {
        return $this->belongsTo(PollElection::class);
    }

    public function pollEntries()
    {
        return $this->hasMany(PollEntry::class, 'poll_election_watcher_id');
    }
}
