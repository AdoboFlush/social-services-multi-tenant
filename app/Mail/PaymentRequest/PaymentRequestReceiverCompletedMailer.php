<?php

namespace App\Mail\PaymentRequest;

use App\Mail\BaseMailer;

class PaymentRequestReceiverCompletedMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Deposit Notification - Payment Request';
    const HEADER = '';
    const LINK_TEXT = '';

    const ACCOUNT_PERSONAL = 'personal';
    const ACCOUNT_BUSINESS = 'business';

    public function __construct($data, $sendData)
    {
        parent::__construct('');

        $this->data = $data->receiver;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("Your payment request has been successfully approved and deposited to your Oriental Wallet account.")."</div><br/>";

        $this->message .= "<table>
            <tr><td colspan='2'>". _lang('Request Details') ."</td></tr>
            <tr><td>" . _lang('Requested Date and Time') . "</td><td>" . $data->created_at . "</td></tr>
            <tr><td>" . _lang('Requested Amount') . "</td><td>" . $sendData->credit_currency . ' ' . formatAmount($sendData->credit_amount) . "</td></tr>
            <tr><td>" . _lang('Fee') . "</td><td>" . $sendData->credit_currency . ' ' . formatAmount($sendData->credit_fee) . "</td></tr>
            <tr><td>" . _lang('Total Deposit Amount') . "</td><td>" . $sendData->credit_currency . ' ' . formatAmount($sendData->credit_total) . "</td></tr>
            <tr><td>" . _lang('Message') . "</td><td>" . $data->description . "</td></tr>
            </table><br/><br/>";

        $this->message .= "<table>
            <tr><td colspan='2'>". _lang('Approver Details') ."</td></tr>
            <tr><td>" . _lang('Approved Date and Time') . "</td><td>" . $data->updated_at . "</td></tr>
            <tr><td>" . _lang('Approver Account') . "</td><td>" . $data->sender->first_name . ' ' . $data->sender->last_name . '(' . $data->sender->account_number .')' . "</td></tr>
            <tr><td>" . _lang('Approved Amount') . "</td><td>" . $sendData->debit_currency . ' ' . formatAmount($sendData->debit_amount) . "</td></tr>
            <tr><td>" . _lang('Message') . "</td><td>" . $data->approve_note   . "</td></tr>
            <tr><td>" . _lang('Reference Number') . "</td><td>" . $data->transaction_number   . "</td></tr>
            </table><br/><br/>";

        $this->message .= "<div>"._lang("Please check your balance on your Oriental Wallet Account.")."</div><br/>";
        $this->message .= "<div>"._lang("The details of this transaction can be viewed on your transaction history.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
