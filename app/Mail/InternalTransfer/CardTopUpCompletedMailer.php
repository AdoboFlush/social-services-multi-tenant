<?php

namespace App\Mail\InternalTransfer;

use App\Mail\BaseMailer;

class CardTopUpCompletedMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Card Top-up Completed';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("Your card top-up request has been successfully loaded to your prepaid card.")."</div><br/>";

        $this->message .= "<table>
            <tr><td>" . _lang('Date and Time') . "</td><td>" . $data->transactionDate . "</td></tr>
            <tr><td>" . _lang('Card Top-up Amount') . "</td><td>" . $data->sentAmount . "</td></tr>
            <tr><td>" . _lang('Reference Number') . "</td><td>" . $data->transactionNumber . "</td></tr>
            </table><br/><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
