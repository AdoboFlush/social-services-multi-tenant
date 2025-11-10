<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaintenanceAffiliates extends Model
{
    protected $table = 'maintenance_affiliates';

    protected $fillable = [
        'maintenance_id',
        'applies_to',
        'exception',
    ];
}
