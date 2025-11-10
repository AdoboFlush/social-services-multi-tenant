<?php

namespace App\Mail\InternalTransfer;

use App\Mail\BaseMailer;

class CardTopUpReceiverRequestMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Deposit Notification via Card Top-up';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $total = floatval(str_replace(',', '', $data->cardTopUpPayload->amount)) + floatval(str_replace(',', '', $data->cardTopUpPayload->fee));
        $total = formatAmount($total);

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("The funds via card top-up has been successfully deposited to your account.")."</div><br/>";

         $this->message .= "<table>
            <tr><td>" . _lang('Date and Time') . "</td><td>" . $data->transactionDate . "</td></tr>
            <tr><td>" . _lang('Card Top-up Amount') . "</td><td>" . $data->cardTopUpPayload->currency . " ".  $data->cardTopUpPayload->amount . "</td></tr>
            <tr><td>" . _lang('Top-up Fee') . "</td><td>" . $data->cardTopUpPayload->currency . " ". $data->cardTopUpPayload->fee . "</td></tr>
             <tr><td>" . _lang('Total Deposit Amount') . "</td><td>" .  $data->cardTopUpPayload->currency . " " . $total . "</td></tr>
            <tr><td>" . _lang('Reference Number') . "</td><td>" . $data->transactionNumber . "</td></tr>
            </table><br/><br/>";

        $this->message .= "<div>"._lang("Please check your balance on your Oriental Wallet Account.")."</div><br/>";
        $this->message .= "<div>"._lang("The details of this transaction can be viewed on your transaction history.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');

    }
}
