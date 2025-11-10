<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KycStatus extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    public const UNREVIEWED = 'unreviewed';
    public const VERIFIED = 'verified';
    public const W_CHECK = 'w-check';
    public const WC_PENDING = 'wc-pending';
    public const WC_DENIED = 'wc-denied';
    public const PENDING = 'pending';
    public const CARD_APPROVED = 'card-approved';
    public const CARD_REJECTED = 'card-rejected';
    public const CARD_PENDING = 'card-pending';
    public const CHECK_A = 'check-a';
    public const CHECK_B = 'check-b';
    public const CHECK_C = 'check-c';
    public const CHECK_D = 'check-d';
    public const CHECK_E = 'check-e';
    public const CHECK_F = 'check-f';
    public const CHECK_G = 'check-g';
    public const CHECK_H = 'check-h';
    public const CHECK_I = 'check-i';
    public const CHECK_J = 'check-j';
}
