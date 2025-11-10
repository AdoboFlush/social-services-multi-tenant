<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    protected $appends = ['last_activity_time_ago'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getLastActivityTimeAgoAttribute(): string
    {
        return Carbon::parse($this->last_activity)->diffForHumans();
    }
}
