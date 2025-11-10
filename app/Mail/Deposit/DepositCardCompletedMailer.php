<?php

namespace App\Mail\Deposit;

use App\Mail\BaseMailer;

class DepositCardCompletedMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Deposit Completed via Mastercard/JCB';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("The funds have been successfully deposited to your Oriental Wallet account.")."</div><br/>";

        $amount = formatAmount($data->amount);
        $fee = formatAmount($data->fee);
        $total = formatAmount($data->total);

        $this->message .= "<table>
            <tr><td>" . _lang("Date and Time") ."</td><td>{$data->created_at}</td></tr>
            <tr><td>" . _lang("Deposit Amount") ."</td><td>{$data->currency} {$amount}</td></tr>
            <tr><td>" . _lang("Fee") ."</td><td>{$data->currency} {$fee}</td></tr>
            <tr><td>" . _lang("Total Deposit") ."</td><td>{$data->currency} {$total}</td></tr>
            <tr><td>" . _lang("Reference Number") ."</td><td>{$data->transaction_number}</td></tr>
            </table><br/><br/>";

        $this->message .= "<div>"._lang("Please check your balance on your Oriental Wallet Account.")."</div><br/>";
        $this->message .= "<div>"._lang("The details of this transaction can be viewed on your transaction history.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
