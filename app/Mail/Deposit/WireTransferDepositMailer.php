<?php

namespace App\Mail\Deposit;

use App\Mail\BaseMailer;
use Auth;

class WireTransferDepositMailer extends BaseMailer
{
    const SUBJECT = 'Deposit Request via Wire Transfer';
    const HEADER = 'Oriental Wallet - Deposit via Local Bank Transfer (Japan) Notification';
    const LINK_TEXT = '';
    
    public function __construct($data)
    {
        parent::__construct('');

        $user = Auth::user();
        $name = $user->first_name . " " . $user->last_name;

        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER;
        $this->message = "Dear Customer Support, <br><br> {$name} {$user->account_number} is requesting to deposit using the following details:<br><br>";

        $amount = formatAmount($data->amount);

        $this->message .= "<table>
            <tr><td>Deposit Bank Name</td><td>{$data->bank_name}</td></tr>
            <tr><td>Account Name</td><td>{$data->account_name}</td></tr>
            <tr><td>Bank Country</td><td>{$data->bank_country}</td></tr>
            <tr><td>Currency</td><td>{$data->currency}</td></tr>
            <tr><td>Amount</td><td>{$amount}</td></tr>
            <tr><td>Note</td><td>{$data->note}</td></tr>
            </table><br/><br/>";

        $this->message .= "Thank you.";
        
        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
