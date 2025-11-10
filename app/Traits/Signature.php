<?php namespace App\Traits;

use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Log;

trait Signature
{

    protected $type_withdrawal = 'INTERNAL_WITHDRAWAL';
    protected $type_deposit = 'INTERNAL_DEPOSIT';
    /*
        Creates digital signature for the user
        This is for business type only
    */
    public function checkUserSignature($mid, $signature) 
    {
        $user = \App\User::where('mid', $mid)->first();

        $validate = $this->validateUser($user);

        if (!empty($validate)) {
            return $validate;
        }

        $generatedSignature = strtoupper($this->generateUserHashSignature($user)); 
        $signature = strtoupper($signature);

        if ($generatedSignature == $signature) {
            return [
                'valid' => true,
                'message' => 'Valid Signture.'
            ];
        }   

        return [
            'valid' => false,
            'message' => 'Invalid Signture.'
        ];
    }

    public function createMid()
    {
        $letters = self::randomString(3);

        $numbers = rand(0,999999);

        $numbers = sprintf("%06d", $numbers);

        $mid = $letters  . '-' . $numbers;
        return $mid;
    }

    public function createTransactionSignature($payload)
    {
        $user = \App\User::where('id', $payload->user_id)->first();
        if (is_null($user)) {
            return [
                'valid' => false,
                'message' => 'User not found - invalid MID.'
            ];
        }


        $param = new \stdClass();
        $mid = $user->mid;
        $account_number = $user->account_number;
        $email = $user->email;

        $transaction_number = isset($payload->transaction_number) ? $payload->transaction_number : '';
        $account_id = isset($payload->account_id) ? $payload->account_id : '';
        $merchant_account_id = isset($payload->merchant_account_id) ? $payload->merchant_account_id : '';
        $transaction_id = isset($payload->transaction_id) ? $payload->transaction_id : '';
        $currency = isset($payload->currency) ? $payload->currency : '';
        $amount = isset($payload->amount) ? $payload->amount : '';

        $type = isset($payload->type) ? $payload->type : '';

        $signature = ''; 
        switch ($type) {
            case $this->type_withdrawal:
                if (!empty($transaction_id)) {
                    $signature = hash('sha256', $mid . $transaction_id  . $account_number . $email . $this->type_withdrawal);
                } else {
                    $signature = hash('sha256', $mid . $merchant_account_id . $currency . $amount . $account_number . $email . $this->type_withdrawal);
                }
                break;
            case $this->type_deposit:
                $signature = hash('sha256', $mid . $account_id . $currency . $amount . $account_number . $email . $this->type_deposit);
                break;
            default:             
                $signature =  hash('sha256', $mid . $transaction_number . $account_number . $email);
                break;    
        }

        return $signature;
    }

    private function generateUserHashSignature($payload)
    {
        $mid = $payload->mid;
        $account_number = $payload->account_number;
        $email = $payload->email;

        return hash('sha256', $mid . $account_number . $email);
    }

    private function generateTransactionHashSignature($payload)
    {
        $mid = $payload->mid;
        $transaction_number = $payload->transaction_number;
        $account_number = $payload->account_number;
        $email = $payload->email;

        if (isset($payload->type) && $payload->type == $this->type_internal_withdrawal && isset($payload->transaction_id) ) {
            $transaction_id = $payload->transaction_id;
            return hash('sha256', $mid . $transaction_id  . $account_number . $email . $this->type_internal_withdrawal);            
        }

        return hash('sha256', $mid . $transaction_number . $account_number . $email);
    }

    private function validateUser($user) {
        if (is_null($user)) {
            return [
                'valid' => false,
                'message' => 'User not found - invalid MID.'
            ];
        }

        if ($user->account_type == 'personal') {
            return [
                'valid' => false,
                'message' => 'Invalid user account type.'
            ];
        }

        return [];
    }    

    private static function randomString($n) { 
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $randomString = ''; 
      
        for ($i = 0; $i < $n; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $randomString .= $characters[$index]; 
        } 
      
        return $randomString; 
    }
}
