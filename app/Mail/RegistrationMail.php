<?php

namespace App\Mail;

use App\Mail\BaseMailer;

class RegistrationMail extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Registered Email Confirmation';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("Please click the link below to complete the registration process, the link is valid for 24 hours.")."</div><br/>";
        $this->message .= '<div><a href="'. $data->verification_url  . '" class="btn btn-email">' . _lang('Verify Email') . '</a></div><br /><br />';

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
