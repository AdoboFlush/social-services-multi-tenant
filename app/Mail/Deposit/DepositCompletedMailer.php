<?php

namespace App\Mail\Deposit;

use App\Mail\BaseMailer;

class DepositCompletedMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Deposit Completed via Local Bank Transfer (Japan)';
    const HEADER = 'Oriental Wallet - Deposit Completed via Local Bank Transfer (Japan)';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        $name = $data->first_name . " " . $data->last_name;
        $account_number = $data->account_number;

        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER;
        $this->message = _lang("Dear Mr/Ms {$name} {$account_number}, <br><br> Thank you for using Oriental Wallet.<br><br>The funds have been successfully deposited to your Oriental Wallet account.<br><br>");

        $amount = formatAmount($data->amount);
        $fee = formatAmount($data->fee);
        $total = formatAmount($data->total);

        $this->message .= "<table>
            <tr><td>Bank Name</td><td>{$data->bankName}</td></tr>
            <tr><td>Branch Name</td><td>{$data->shitenName}</td></tr>
            <tr><td>Account Type</td><td>{$data->kouzaType}</td></tr>
            <tr><td>Account Number</td><td>{$data->kouzaNm}</td></tr>
            <tr><td>Account Name</td><td>{$data->kouzaMeigi}</td></tr>
            <tr><td>Transfer ID</td><td>{$data->nameId}</td></tr>
            <tr><td>Amount</td><td>{$amount}</td></tr>
            <tr><td>Fee</td><td>{$fee}</td></tr>
            <tr><td>Total Deposit</td><td>{$total}</td></tr>
            </table><br/><br/>";

        $this->message .= _lang("Please check your balance on your Oriental Wallet Account.<br/>");
        $this->message .= _lang("The details of this transaction can be viewed on your Oriental Wallet account's transaction history.");

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');

        parent::__construct('');
    }
}
