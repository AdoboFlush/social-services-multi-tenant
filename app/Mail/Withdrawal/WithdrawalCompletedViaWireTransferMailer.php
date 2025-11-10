<?php

namespace App\Mail\Withdrawal;

use App\Mail\BaseMailer;
use Carbon\Carbon;

class WithdrawalCompletedViaWireTransferMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Withdrawal Completion Notice via Wire Transfer';
    const HEADER = 'Oriental Wallet - Withdrawal Completion Notice via Wire Transfer';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("Your withdrawal request has been processed.")."</div><br/>";

        $debit_amount = formatAmount($data->debit_amount);
        $fee = formatAmount($data->fee);
        $total = formatAmount($data->total);

        $date = Carbon::parse($data->updated_at)->format('Y-m-d H:i A');

        $this->message .= "<table>
            <tr><td>" . _lang("Date and Time") ."</td><td>{$date}</td></tr>
            <tr><td>" . _lang("Beneficiary Amount") ."</td><td>{$data->currency} {$data->amount}</td></tr>
            <tr><td>" . _lang("Withdrawal Amount") ."</td><td>{$data->debit_currency} {$debit_amount}</td></tr>
            <tr><td>" . _lang("Fees") ."</td><td>{$data->debit_currency} {$fee}</td></tr>
            <tr><td>" . _lang("Total Withdrawal Amount") ."</td><td>{$data->debit_currency} {$total}</td></tr>
            <tr><td>" . _lang("Message") ."</td><td>{$data->reference_message}</td></tr>
            <tr><td>" . _lang("Reference Number") ."</td><td>{$data->transaction_number}</td></tr>
            </table><br/><br/>";

        $this->message .= "<div>"._lang("Please check your balance on your Oriental Wallet Account.")."</div><br/>";
        $this->message .= "<div>"._lang("The details of this transaction can be viewed on your transaction history.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
