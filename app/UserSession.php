<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_agent',
        'ip_address',
        'method',
    ];

    public const METHOD_OWL = 'oriental wallet';
    public const METHOD_API = 'api';

    public function user(): BelongsTo
    {
		return $this->belongsTo('App\User','user_id');
	}
		
}
