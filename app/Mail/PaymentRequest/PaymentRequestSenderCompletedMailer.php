<?php

namespace App\Mail\PaymentRequest;

use App\Mail\BaseMailer;

class PaymentRequestSenderCompletedMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Withdrawal Notification - Payment Request';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data, $sendData)
    {
        $this->data = $data->sender;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("You have successfully approved the payment request, and the funds have been withdrawn from your account.")."</div><br/>";

        $this->message .= "<table>
            <tr><td colspan='2'>". _lang('Approved Details') ."</td></tr>
            <tr><td>" . _lang('Approved Date and Time') . "</td><td>" . $data->updated_at . "</td></tr>
            <tr><td>" . _lang('Approved Amount') . "</td><td>" . $sendData->debit_currency . ' ' . formatAmount($sendData->debit_amount) . "</td></tr>
            <tr><td>" . _lang('Fee') . "</td><td>" . $sendData->debit_currency . ' ' . formatAmount($sendData->debit_fee) . "</td></tr>
            <tr><td>" . _lang('Total Withdrawal Amount') . "</td><td>" . $sendData->debit_currency . ' ' . formatAmount($sendData->debit_total) . "</td></tr>
            <tr><td>" . _lang('Message') . "</td><td>" . $data->approve_note . "</td></tr>
            </table><br/><br/>";            


        $this->message .= "<table>
            <tr><td colspan='2'>". _lang('Request Details') ."</td></tr>
            <tr><td>" . _lang('Request Date and Time') . "</td><td>" . $data->created_at . "</td></tr>
            <tr><td>" . _lang('Request Account') . "</td><td>" . $data->receiver->first_name . ' ' . $data->receiver->last_name . '(' . $data->receiver->account_number .')' . "</td></tr>
            <tr><td>" . _lang('Approved Amount') . "</td><td>" . $sendData->credit_currency. ' ' . formatAmount($sendData->credit_amount) . "</td></tr>
            <tr><td>" . _lang('Message') . "</td><td>" . $data->description   . "</td></tr>
            <tr><td>" . _lang('Reference Number') . "</td><td>" . $data->transaction_number   . "</td></tr>
            </table><br/><br/>";

        $this->message .= "<div>"._lang("Please check your balance on your Oriental Wallet Account.")."</div><br/>";
        $this->message .= "<div>"._lang("The details of this transaction can be viewed on your transaction history.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
