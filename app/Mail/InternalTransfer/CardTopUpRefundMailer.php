<?php

namespace App\Mail\InternalTransfer;

use App\Mail\BaseMailer;

class CardTopUpRefundMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet -Cancelled Card Top-up Request';
    const HEADER = 'Oriental Wallet -Cancelled Card Top-up Request';

    public function __construct($data)
    {
        $name = $data->first_name . " " . $data->last_name;

        $this->emailSubject = _lang(Self::SUBJECT);
        $this->header = _lang(Self::HEADER);
       
        $this->message .= _lang('Dear Mr/Ms {name} {account_number},', ['name' => $name, 'account_number' => $data->account_number]). '<br/><br/>';

        $this->message .= _lang("Thank you for using Oriental Wallet.")."<br><br>";
        $this->message .= _lang("Your card top-up request has been cancelled and the funds have been credited back to your Oriental Wallet account.")."<br><br>";

        $this->message .= "<table>
            <tr><td>"._lang("Date and Time")."</td><td>{$data->created_at}</td></tr>
            <tr><td>"._lang("Reference Number")."</td><td>{$data->transaction_number}</td></tr>
            </table><br/><br/>";
        
        $this->message .= _lang("Please check your balance on your Oriental Wallet Account.")."<br/>";
        $this->message .= _lang("The details of this transaction can be viewed on your transaction history.")."<br/><br/>";
        $this->message .= _lang("If you have any questions or concerns, please send us a message via ticket through your Oriental Wallet Account.")."<br/>";
        $this->message .= _lang("Thank you for your continued patronage. We are committed to providing our customers with high-quality service.")."<br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');

        parent::__construct('');
    }
}
