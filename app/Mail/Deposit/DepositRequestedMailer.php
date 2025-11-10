<?php

namespace App\Mail\Deposit;

use App\Mail\BaseMailer;

class DepositRequestedMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Deposit via Local Bank Transfer (Japan) Notification';
    const HEADER = 'Oriental Wallet - Deposit via Local Bank Transfer (Japan) Notification';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $amount = formatAmount($data->amount);
        $fee = formatAmount($data->fee);
        $total = formatAmount($data->total);

        $this->message = "<div>". _lang("Please use the following bank details to complete your deposit.")."</div></br>";
        $this->message .= "<table>
            <tr><td>"._lang("Bank Name")."</td><td>{$data->bankName}</td></tr>
            <tr><td>"._lang("Branch Name")."</td><td>{$data->shitenName}</td></tr>
            <tr><td>"._lang("Account Type")."</td><td>{$data->kouzaType}</td></tr>
            <tr><td>"._lang("Account Number")."</td><td>{$data->kouzaNm}</td></tr>
            <tr><td>"._lang("Account Name")."</td><td>{$data->kouzaMeigi}</td></tr>
            <tr><td>"._lang("Transfer ID")."</td><td>{$data->nameId}</td></tr>
            <tr><td>"._lang("Amount")."</td><td>JPY {$amount}</td></tr>
            <tr><td>"._lang("Fee")."</td><td>JPY {$fee}</td></tr>
            <tr><td>"._lang("Total Deposit")."</td><td>JPY {$total}</td></tr>
            </table><br/><br/>";

        $this->message .= "<strong>"._lang('NOTICE').":</strong>";
        $this->message .= "<ol type='a'>";
        $this->message .= "<li>"._lang('Please indicate the generated Transfer ID on the "account name" field when making a deposit [Transfer ID + Full Name in KATAKANA]. Without the Transfer ID, the deposit may take some time to be credited on your Oriental Wallet Account.')."</li>";
        $this->message .= "<li>"._lang('Deposits made after 23:00 JST during weekdays and 18:00 JST during Saturday, Sunday and Holidays will be reflected on your Oriental Wallet Account the following banking day.')."</li>";
        $this->message .= "<li>"._lang('Please keep the remittance/deposit statement as it is necessary when making an inquiry.')."</li>";
        $this->message .= "<li>"._lang('Cancellation of deposit is not available on this method. You may withdraw the funds once reflected on your Oriental Wallet Account.')."</li></ol>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
