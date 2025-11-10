<?php namespace App\Traits;

use Carbon\Carbon;
use App\Transaction;
use Illuminate\Support\Facades\Log;

trait Transact
{
    
    public function createTransactionId($type) 
    {
        $transaction_id = '';
        $model = '';
        switch ($type) {
            case 'withdrawal':
                $transaction_id = 'WD';     
                $model = app("App\WireTransfer");
                break;

            case 'internal transfer':
                $transaction_id = 'IT';
                $model = app("App\InternalTransfer");
                break;

            case 'exchange_currency':
                $transaction_id = 'EX';
                $model = app("App\ExchangeCurrency");
                break;

            case 'dormant_fee':
                $transaction_id = 'DF';
                $model = app("App\Transaction");
                break;
            
            case 'inactivity_fee':
                $transaction_id = 'DF';
                $model = app("App\Transaction");
                break;
                
            case 'monthly_fee':
                $transaction_id = 'MF';
                $model = app("App\Transaction");
                break;

            case 'payment_request':
                $transaction_id = 'PR';
                $model = app("App\PaymentRequest");
                break;

            case 'card_topup':
                $transaction_id = 'CT';
                $model = app("App\CardTopUp");
                break;
            
            default:
                //deposit
                $transaction_id = 'DP';
                $model = app("App\Deposit");
                break;
        }
        $generated_id = $this->createID($transaction_id, $model);


        return $generated_id;

    }

    private function createID($transaction, $model) : string {
        $count = rand(1, 99999);

        $num_padded = sprintf("%05d", $count);
        $transaction_id = $transaction . Carbon::now()->format('Ymd') . $num_padded;

        $result = $model::where('transaction_number', $transaction_id)->first();
        if (!is_null($result)) {
            return $this->createID($transaction, $model);
        }

        return $transaction_id;
    }
}
