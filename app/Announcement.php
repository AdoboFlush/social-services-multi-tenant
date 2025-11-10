<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $fillable = [
        'title',
        'content',
        'language',
        'publish',
        'published_at',
        'created_by'
    ];

    public function user(){
        return $this->belongsTo('App\User','created_by');
    }
}