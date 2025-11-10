<?php

namespace App\Mail\InternalTransfer;

use App\Mail\BaseMailer;

class CardTopUpSenderRequestMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Withdrawal Notification via Card Top-up';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("Your card top-up request has been successfully submitted, and the funds have been withdrawn from your account.")."</div><br/>";

        $this->message .= "<table>
            <tr><td>" . _lang('Date and Time') . "</td><td>" . $data->transactionDate . "</td></tr>
            <tr><td>" . _lang('Card Top-up Amount') . "</td><td>" . $data->cardTopUpPayload->currency . " ".  $data->cardTopUpPayload->amount . "</td></tr>
            <tr><td>" . _lang('Top-up Fee') . "</td><td>" . $data->cardTopUpPayload->currency . " ". $data->cardTopUpPayload->fee . "</td></tr>
            <tr><td>" . _lang('Total Withdrawal Amount') . "</td><td>" . $data->sentAmount . "</td></tr>
            <tr><td>" . _lang('Reference Number') . "</td><td>" . $data->transactionNumber . "</td></tr>
            </table><br/><br/>";


        $this->message .= "<div>"._lang("Please check your balance on your Oriental Wallet Account.")."</div><br/>";
        $this->message .= "<div>"._lang("The details of this transaction can be viewed on your transaction history.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
