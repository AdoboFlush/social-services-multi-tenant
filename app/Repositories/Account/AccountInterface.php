<?php

namespace App\Repositories\Account;
use App\Repositories\BaseInterface;

interface AccountInterface extends BaseInterface
{
    public function updateAmountByUserIdAndCurrency($id, $currency, $amount);
    public function getAccountsByUserId($user_id);
    public function where($payload, $get = 'get');
    public function firstOrCreate($request);
}
