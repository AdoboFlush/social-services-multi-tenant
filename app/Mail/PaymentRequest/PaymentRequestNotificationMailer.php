<?php

namespace App\Mail\PaymentRequest;

use App\Mail\BaseMailer;

class PaymentRequestNotificationMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Payment Request Notification';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data->sender;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("You have received a payment request. Please login to your Oriental Wallet account to check the details.")."</div><br/>";

        $this->message .= "<table>
            <tr><td colspan='2'>". _lang('Request Details') ."</td></tr>
            <tr><td>" . _lang('Requested Date and Time') . "</td><td>" . $data->created_at . "</td></tr>
            <tr><td>" . _lang('Requester Account') . "</td><td>" . $data->receiver->first_name . ' ' . $data->receiver->last_name . '(' . $data->receiver->account_number .')' . "</td></tr>
            <tr><td>" . _lang('Requested Amount') . "</td><td>" . $data->currency . ' ' . formatAmount($data->amount) . "</td></tr>
            <tr><td>" . _lang('Message') . "</td><td>" . $data->description . "</td></tr>
            <tr><td>" . _lang('Reference Number') . "</td><td>" . $data->transaction_number . "</td></tr>
            </table><br/><br/>";

        $this->message .= "<div>"._lang("Please check your balance on your Oriental Wallet Account.")."</div><br/>";
        $this->message .= "<div>"._lang("The details of this transaction can be viewed on your transaction history.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
