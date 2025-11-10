<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maintenance extends Model
{
    public const JP_VOUCHER = 'jp_voucher';
    public const DEBIT_CREDIT_CARD = 'debit_credit_card';

    public const ACTIVE = 1;

    protected $table = 'maintenance';

    protected $fillable = [
        'content',
        'jp_content',
    ];

    public function affiliate_codes(): HasMany
    {
        return $this->hasMany(MaintenanceAffiliates::class, 'maintenance_id', 'id');
    }
}
