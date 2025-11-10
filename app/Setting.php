<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    const MAINTENANCE_ACTIVE = '1';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $fillable = ["value","name"];

    protected $table = 'settings';

    protected static $logName = 'Settings';

    protected static $ignoreChangedAttributes = ['created_at','updated_at'];

    protected static $logAttributes = ["value","name"];

    protected static $logOnlyDirty = true;

    public function logDetails()
    {
        return null;
    }
}