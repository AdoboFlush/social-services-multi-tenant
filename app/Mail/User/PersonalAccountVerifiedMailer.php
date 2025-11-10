<?php


namespace App\Mail\User;

use App\Mail\BaseMailer;

class PersonalAccountVerifiedMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Account Upgraded and Verified.';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');
        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message .= "<div>". _lang("Your account has been verified and upgraded. Your withdrawal/remittance limit of EUR 2,000 has been lifted. You may now withdraw funds using our money-out services up to EUR 50,000 (or amount equivalent to other supported currencies), and unlimited internal transfers.")."</div><br/>";
        $this->message .= "<div>". _lang("Please note that Oriental Wallet may still ask for additional KYC documents for verification purposes.")."<div/><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
