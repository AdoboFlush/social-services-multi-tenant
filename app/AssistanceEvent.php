<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssistanceEvent extends Model
{
    protected $fillable = [
        'name',
        'description',
        'starts_at',
        'ends_at',
        'assistance_type',
        'is_active',
        'amount',
        'custom_condition_props',
    ];

    public function assistances() : HasMany
    {
        return $this->hasMany(VoterHasAssistance::class, "assistance_event_id");
    }
}
