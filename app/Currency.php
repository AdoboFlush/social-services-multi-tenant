<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Currency extends Model
{
    public const ACTIVE = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'currency';

    protected $fillable = [
        'name',
        'status',
    ];

    public function logDetails()
    {
        return null;
    }
    
}
