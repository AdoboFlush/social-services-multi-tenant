<?php

namespace App\Repositories\Currency;
use App\Repositories\BaseInterface;

interface CurrencyInterface extends BaseInterface
{
	public function getAllActive($pluck = false);
}
