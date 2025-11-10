<?php

namespace App\Mail\InternalTransfer;

use App\Mail\BaseMailer;

class ExchangeCurrencyRequestMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Currency Exchange Notice';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("Your currency exchange transaction has been completed.")."</div><br/>";

        $this->message .= '<table>
            <tr><td>' . _lang('Date and Time')  . '</td><td>' . $data->transaction_date . '</td></tr>
            <tr><td>' . _lang('Debit Currency Amount')  . '</td><td>' .$data->sent_amount . '</td></tr>
            <tr><td>' . _lang('Beneficiary Currency Amount')  . '</td><td>' . $data->received_amount . '</td></tr>
            <tr><td>' . _lang('Reference Number')  . '</td><td>' . $data->transaction_number . '</td></tr>    
            </table><br/><br/>';

        $this->message .= "<div>"._lang("Please check your balance on your Oriental Wallet Account.")."</div><br/>";
        $this->message .= "<div>"._lang("The details of this transaction can be viewed on your transaction history.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
