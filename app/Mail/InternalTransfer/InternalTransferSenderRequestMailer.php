<?php

namespace App\Mail\InternalTransfer;

use App\Mail\BaseMailer;

class InternalTransferSenderRequestMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Withdrawal Notification via Internal Transfer';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("The funds has been successfully withdrawn from your Oriental Wallet account.")."</div><br/>";

        $this->message .= "<table>
            <tr><td>" . _lang('Date and Time') . "</td><td>" . $data->transactionDate . "</td></tr>
            <tr><td>" . _lang('Withdrawal Amount') . "</td><td>" . $data->amount . "</td></tr>
            <tr><td>" . _lang('Fees') . "</td><td>" . $data->senderFee . "</td></tr>
            <tr><td>" . _lang('Total Withdrawal Amount') . "</td><td>" .$data->sentAmount . "</td></tr>
            <tr><td>" . _lang('Beneficiary Account') . "</td><td>" .
            ucfirst($data->beneficiaryFirstName) . ' ' . ucfirst($data->beneficiaryLastName) .
            ' - ' . '(' . $data->beneficiaryAccountNumber . ')'
            . "</td></tr>
            <tr><td>" . _lang('Beneficiary Amount') . "</td><td>" . $data->receivedAmount . "</td></tr>
            <tr><td>" . _lang('Message') . "</td><td>" . $data->note . "</td></tr>
            <tr><td>" . _lang('Reference Number') . "</td><td>" . $data->transactionNumber . "</td></tr>
            </table><br/><br/>";


        $this->message .= "<div>"._lang("Please check your balance on your Oriental Wallet Account.")."</div><br/>";
        $this->message .= "<div>"._lang("The details of this transaction can be viewed on your transaction history.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
