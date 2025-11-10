<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
class Transaction extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';
    protected $fillable = [
        'user_id',
        'currency',
        'amount',
        'transaction_number',
        'fee',
        'rate',
        'account_id',
        'dr_cr',
        'type',
        'status',
        'note',
        'ref_id',
        'parent_id',
        'current_balance',
        'updated_by',
        'created_by',
        'updated_at',
        'approval_date'
    ];

    public const DEPOSIT = 'deposit';
    public const WITHDRAWAL = 'withdrawal';

    protected $appends = ['method'];

    public const CREDIT_DEBIT_CARD = 'credit/debit card';
    public const SEA_BANK_DEPOSIT = 'sea_deposit';

    public const TYPE_INTERNAL_TRANSFER = 'internal_transfer';
    public const TYPE_PAYMENT_REQUEST = 'payment_request';
    public const TYPE_BULK_WITHDRAW = 'bulk_withdrawal';
    public const TYPE_EXCHANGE_RATE = 'currency_exchange';
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_REFUND = 'refund';
    public const TYPE_WITHDRAWAL = 'withdrawal';
    public const TYPE_WIRE_TRANSFER = 'wire_transfer';
    public const TYPE_CARD_TOPUP = 'card_topup';
    public const TYPE_INACTIVITY_FEE = 'inactivity_fee';
    public const TYPE_DORMANCY_FEE = 'dormancy_fee';
    public const TYPE_MONTHLY_FEE = 'monthly_fee';

    public const STATUS_COMPLETED = 'completed';
    public const STATUS_APPLYING = 'applying';
    public const STATUS_CANCELED = 'canceled';

    public const USER_TYPE_ADMIN = 'admin';
    public const USER_TYPE_MANUAL = 'manual';
    public const USER_TYPE_USER = 'user';

    public function parentUser()
    {
        return $this->hasOneThrough('App\User', 'App\Transaction', 'parent_id', 'id', 'id', 'user_id')->withDefault();
    }

    public function childUser(): HasOneThrough
    {
        return $this->hasOneThrough('App\User', 'App\Transaction', 'id', 'id', 'parent_id', 'user_id')->withDefault();
    }

    public function created_user()
    {
        return $this->belongsTo('App\User', 'created_by')->withDefault();
    }

    public function updated_user()
    {
        return $this->belongsTo('App\User', 'updated_by')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id')->withDefault();
    }

    public function account()
    {
        return $this->belongsTo('App\Account', 'account_id')->withDefault();
    }

    public function wire_transfer()
    {
        return $this->hasOne('App\WireTransfer', 'id', 'ref_id');
    }

    public function credit()
    {
        return $this->hasOne('App\Transaction', 'parent_id')->where('type', 'transfer')->withDefault();
    }

    public function card_transfer()
    {
        return $this->hasOne('App\CardTransaction', 'transaction_id')->withDefault();
    }

    public function internal_transfer()
    {
        return $this->hasOne('App\Transaction', 'parent_id')
            ->where('type', 'internal_transfer')
            ->withDefault();
    }

    public function scopeRelated($query)
    {
        return $query
            ->when($this->type === 'withdrawal', function ($q) {
                return $q->with('wire_transfer');
            });
    }

    public function deposit()
    {
        return $this->hasOne('App\Deposit', 'id', 'ref_id');
    }

    public function bulkWithdrawals()
    {
        return $this->hasMany('App\WireTransfer', 'transaction_number', 'transaction_number');
    }

    public function internalTransfer()
    {
        return $this->hasOne('App\InternalTransfer', 'id', 'ref_id');
    }

    public function logDetails()
    {
        return $this->user->first_name . " " . $this->user->last_name . " " . $this->user->account_number . "\n" . $this->transaction_number;
    }

    public function parent()
    {
        return $this->hasOne('App\Transaction', 'id', 'parent_id')->withDefault();
    }

    public function child(): HasOne
    {
        return $this->hasOne('App\Transaction', 'parent_id', 'id')->withDefault();
    }

    public function depositCard(): HasOne
    {
        return $this->hasOne(DepositCard::class, 'transaction_number', 'transaction_number');
    }

    public function card_topup(): HasOne
    {
        return $this->hasOne(CardTopUp::class, 'transaction_number', 'transaction_number');
    }

    public function payment_request(): HasOne
    {
        return $this->hasOne(PaymentRequest::class, 'transaction_number', 'transaction_number');
    }

    public static function getTransactionsBetweenCreatedDates(
        int $user_id,
        string $transaction_type,
        Carbon $startDate,
        Carbon $endDate
    ): Builder {
        return self::where('user_id', $user_id)
            ->where('type', $transaction_type)
            ->whereBetween('created_at', [
                $startDate,
                $endDate
            ]);
    }

    public function getRateAttribute(): ? float
    {
        if(isset($this->attributes['rate']) && isset($this->attributes['type'])){
            $rate = $this->attributes['rate'];
            if(!$rate) {
                switch ($this->attributes['type']) {
                    case self::TYPE_WITHDRAWAL:
                    case self::TYPE_BULK_WITHDRAW:
                        $rate = $this->wire_transfer->rate;
                        break;
                    case self::TYPE_PAYMENT_REQUEST:
                        $rate = !empty($this->payment_request) ? $this->payment_request->rate : null;
                        break;
                    case self::TYPE_CARD_TOPUP:
                        $rate = !empty($this->card_topup->exchange_rate) ? $this->card_topup->exchange_rate : null;
                        break;
                }
            }
            return $rate;
        } 
        return null;
    }
}
