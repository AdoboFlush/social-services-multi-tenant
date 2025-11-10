<?php

namespace App\Repositories\Transaction;

use App\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionInterface
{
	public function getByPaymentRequestRefId(int $ref_id): Transaction;
}
