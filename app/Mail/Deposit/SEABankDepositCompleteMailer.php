<?php

namespace App\Mail\Deposit;

use App\Deposit;
use App\Mail\BaseMailer;

class SEABankDepositCompleteMailer extends BaseMailer
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Deposit $deposit)
    {
        $this->template = "mail.content";
        $this->data = $deposit;
        $this->emailSubject = _lang('Oriental Wallet - Deposit Completed via LBT (Southeast Asia)');
        $this->message .= "<div>" . _lang("The funds have been successfully deposited to your Oriental Wallet account.") . "</div><br/>";

        $this->message .= "<table>";
        $this->message .= "<tr>";
        $this->message .= "<td>" . _lang("Date and Time") . "</td>";
        $this->message .= "<td>" . date("Y-m-d H:i:s") . "</td>";
        $this->message .= "</tr>";

        $this->message .= "<tr>";
        $this->message .= "<td>" . _lang("Deposit Amount") . "</td>";
        $this->message .= "<td>" . $deposit->currency . " " . number_format($deposit->amount, 2) . "</td>";
        $this->message .= "</tr>";

        $this->message .= "<tr>";
        $this->message .= "<td>" . _lang("Fee") . "</td>";
        $this->message .= "<td>" .  $deposit->currency . " " . number_format($deposit->fee, 2) . "</td>";
        $this->message .= "</tr>";

        $this->message .= "<tr>";
        $this->message .= "<td>" . _lang("Total Deposit") . "</td>";
        $this->message .= "<td>" .  $deposit->currency . " " . number_format($deposit->amount + $deposit->fee, 2) . "</td>";
        $this->message .= "</tr>";

        $this->message .= "<tr>";
        $this->message .= "<td>" . _lang("Reference Number") . "</td>";
        $this->message .= "<td>" . $deposit->transaction_number . "</td>";
        $this->message .= "</tr>";
        $this->message .= "</table><br>";

        $this->message .= "<div>" . _lang("Please check your balance on your Oriental Wallet Account.") . "</div><br/>";
        $this->message .= "<div>" . _lang("The details of this transaction can be viewed on your transaction history.") . "</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');

        // parent::__construct('');
    }
}
