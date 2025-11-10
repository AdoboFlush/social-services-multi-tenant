<?php

namespace App\Mail\Withdrawal;

use App\Mail\BaseMailer;
use Carbon\Carbon;

class WithdrawalCanceledSEA extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Cancellation of Withdrawal via Local Bank Transfer (SEA)';
    const HEADER = 'Oriental Wallet - Cancellation of Withdrawal via Local Bank Transfer (SEA)';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>" . _lang("Your withdrawal request has been cancelled and the funds have been credited back to your Oriental Wallet account.") . "</div><br/>";

        $date = Carbon::parse($data->updated_at)->format('Y-m-d H:i A');

        $this->message .= "<table>
            <tr><td>" . _lang("Date and Time") . "</td><td>{$date}</td></tr>
            <tr><td>" . _lang("Reference Number") . "</td><td>{$data->transaction_number}</td></tr>
            </table><br/><br/>";

        $this->message .= "<div>" . _lang("Please check your balance on your Oriental Wallet Account.") . "</div><br/>";
        $this->message .= "<div>" . _lang("The details of this transaction can be viewed on your transaction history.") . "</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
