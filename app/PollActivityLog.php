<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollActivityLog extends Model
{
    protected $fillable = [
        'action',
        'description',
        'poll_watcher_id',
        'poll_entry_id',
        'poll_election_id',
        'ip_address',
    ];

    public function pollWatcher()
    {
        return $this->belongsTo(PollWatcher::class);
    }

    public function pollEntry()
    {
        return $this->belongsTo(PollEntry::class);
    }

    public function pollElection()
    {
        return $this->belongsTo(PollElection::class);
    }
}
