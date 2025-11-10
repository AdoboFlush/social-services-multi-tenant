<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public const ACTIVE = 1;

    public const BUSINESS = 'business';
    public const PERSONAL = 'personal';

    public const TYPES = [
        self::BUSINESS,
        self::PERSONAL
    ];

    protected $table = 'accounts';

    protected $fillable = [
        'user_id', 'currency', 'status', 'opening_balance',
    ];

    protected $appends = [
        'opening_balance_in_money_format',
        'account_number',
    ];

    public function created_user(){
    	return $this->belongsTo('App\User','created_by')->withDefault();
    }

    public function updated_user(){
    	return $this->belongsTo('App\User','updated_by')->withDefault();
    }

    public function account_type(){
    	return $this->belongsTo('App\AccountType','account_type_id')->withDefault();
    }

    public function owner(){
    	return $this->belongsTo('App\User','user_id')->withDefault();
    }

    public function currency_status(){
        return $this->belongsTo('App\Currency','currency','name');
    }

    public function getOpeningBalanceInMoneyFormatAttribute(): string
    {
        return number_format($this->opening_balance, 2);
    }

    public function getAccountNumberAttribute():? string
    {
        return $this->owner->account_number;
    }
}
