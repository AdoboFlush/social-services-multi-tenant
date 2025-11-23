<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantConnects;

class VoterHasAssistance extends Model
{
    use TenantConnects;
    protected $fillable = [
        "assistance_event_id",
        "voter_tag_detail_id",
        "user_id",
    ]; 
    
    public function assistanceEvent() : BelongsTo
    {
        return $this->belongsTo(AssistanceEvent::class, "assistance_event_id");
    }

    public function voterTagDetail() : BelongsTo
    {
        return $this->belongsTo(VoterTagDetail::class, "voter_tag_detail_id");
    }

    public function processer() : BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
