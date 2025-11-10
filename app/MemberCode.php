<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'code',
        'member_id',
        'active',
        'expire_at',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, "member_id", "id");
    }

}