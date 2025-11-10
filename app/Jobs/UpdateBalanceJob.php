<?php

namespace App\Jobs;

use App\Account;
use App\InternalTransfer;
use App\Repositories\Transaction\TransactionInterface;
use App\Services\Transaction\TransactionFacade;
use App\Transaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateBalanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $account_id;
    protected $transaction_id;

    const LOG_BALANCE_JOB = 'TRANSACTION BALANCE UPDATE';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        int $account_id,
        int $transaction_id
    )
    {
        $this->account_id = $account_id;
        $this->transaction_id = $transaction_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $account = Account::find($this->account_id);
        $currentTransaction = Transaction::find($this->transaction_id);
        $accountBalance = $account->opening_balance;

        $transactionWithBalance = TransactionFacade::getLastTransactionByBalanceOrder($currentTransaction);

        if(isset($transactionWithBalance)){
            $transactionBalance = $transactionWithBalance->current_balance;
        }else{
            $transactionBalance = $account->opening_balance;
        }

        if($currentTransaction->type === Transaction::TYPE_INTERNAL_TRANSFER
            || $currentTransaction->type === Transaction::TYPE_PAYMENT_REQUEST)
        {
            if($currentTransaction->dr_cr === 'cr'){
                // receiver
                $accountBalance = $accountBalance + ($currentTransaction->amount - $currentTransaction->fee);
                $transactionBalance = $transactionBalance + ($currentTransaction->amount - $currentTransaction->fee);

            }else if($currentTransaction->dr_cr === 'dr'){
                // sender
                $accountBalance = $accountBalance - ($currentTransaction->amount + $currentTransaction->fee);
                $transactionBalance = $transactionBalance - ($currentTransaction->amount + $currentTransaction->fee);
            }
        }else if($currentTransaction->type === Transaction::TYPE_DEPOSIT){
            
            $accountBalance = $accountBalance + floatval($currentTransaction->amount);
            $transactionBalance = $transactionBalance + floatval($currentTransaction->amount);

        }else if($currentTransaction->type === Transaction::TYPE_EXCHANGE_RATE){
            if($currentTransaction->dr_cr === 'cr'){
                // receiver
                $accountBalance = $accountBalance + $currentTransaction->amount;
                $transactionBalance = $transactionBalance + $currentTransaction->amount;

            }else if($currentTransaction->dr_cr === 'dr'){
                // sender
                $accountBalance = $accountBalance - $currentTransaction->amount;
                $transactionBalance = $transactionBalance - $currentTransaction->amount;
            }
        }else if($currentTransaction->type === Transaction::TYPE_WITHDRAWAL){

            $accountBalance = $accountBalance
                - (floatval($currentTransaction->wire_transfer->debit_amount) + $currentTransaction->fee);
            $transactionBalance = $transactionBalance 
                - (floatval($currentTransaction->wire_transfer->debit_amount) + $currentTransaction->fee);

        }else if($currentTransaction->type === Transaction::TYPE_REFUND){
            $accountBalance = $accountBalance
                + (floatval($currentTransaction->amount) - $currentTransaction->fee);
            $transactionBalance = $transactionBalance 
                + (floatval($currentTransaction->amount) - $currentTransaction->fee);
        }else if($currentTransaction->type === Transaction::TYPE_DORMANCY_FEE
            || $currentTransaction->type === Transaction::TYPE_INACTIVITY_FEE
            || $currentTransaction->type === Transaction::TYPE_MONTHLY_FEE)
        {
            $accountBalance = $accountBalance
                - (floatval($currentTransaction->amount) + $currentTransaction->fee);
            $transactionBalance = $transactionBalance 
                - (floatval($currentTransaction->amount) + $currentTransaction->fee);
        }else if($currentTransaction->type === Transaction::TYPE_CARD_TOPUP){
            $accountBalance = $accountBalance
                - (floatval($currentTransaction->amount) + $currentTransaction->fee);
            $transactionBalance = $transactionBalance 
                - (floatval($currentTransaction->amount) + $currentTransaction->fee);
        }else if($currentTransaction->type === Transaction::TYPE_BULK_WITHDRAW){

            $accountBalance = $accountBalance
                - (floatval($currentTransaction->amount) + $currentTransaction->fee);
            $transactionBalance = $transactionBalance 
                - (floatval($currentTransaction->amount) + $currentTransaction->fee);

        }

        DB::beginTransaction();

        $account->opening_balance = $accountBalance;

        $currentTransaction->current_balance = $transactionBalance;
        $currentTransaction->balance_order_id = 
            TransactionFacade::getLatestBalanceOrder($currentTransaction) + 1; // increment balance order base on queue
        
        $account->save();
        $currentTransaction->save();

        Log::info(self::LOG_BALANCE_JOB . ' - ' . json_encode($currentTransaction));
        DB::commit();
    }

    /**
     * Handle job failure.
     *
     * @return void
     */
    public function failed(Exception $e)
    {
        Log::error('FAILED JOB - ' . $e->getMessage());
        report($e);
    }
}
