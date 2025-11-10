<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollWatcher extends Model
{
    protected $fillable = ['user_id', 'brgy', 'precinct', 'is_active', 'area', 'poll_place', 'clustered_precincts', 'no_of_registered_voters'];

    protected $appends = ["clustered_precinct_count"];

    public function pollEntries()
    {
        return $this->hasMany(PollEntry::class);
    }

    public function pollElectionWatchers()
    {
        return $this->hasMany(PollElectionWatcher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getClusteredPrecinctCountAttribute()
    {
        if(strpos($this->clustered_precincts, '-') !== false) {
            $clustered_frag = explode("-", $this->clustered_precincts);
            $clustered_precinct_arr = range(intval($clustered_frag[0]), intval($clustered_frag[1]));
        } else {
            $clustered_precinct_arr = [intval($this->clustered_precincts)];
        }
        return count($clustered_precinct_arr ?? 0);
    }

    public function getActiveElection()
    {
        return PollElection::whereHas('pollElectionWatchers', fn($q) => $q->where('poll_watcher_id', $this->id))
            ->first();
    }
}
    