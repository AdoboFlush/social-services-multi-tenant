<?php

namespace App\Mail;

use App\Mail\BaseMailer;

class VerificationSuccessMail extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Successful Verification';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');
        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);
        $this->message .= '<div>' . _lang('You have successfully verified your email. Please click the link below to login.') . '</<div><br/><br/>';
        $this->message .= '<div><a href="'. url('login') . '"class="btn btn-email">' . _lang('Login') . '</div><br/><br/>';

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
