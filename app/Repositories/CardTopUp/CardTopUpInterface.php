<?php

namespace App\Repositories\CardTopUp;
use App\Repositories\BaseInterface;

interface CardTopUpInterface extends BaseInterface
{
	public function sendDepositRequest($request);
}
