<?php

namespace App\Mail\Security;

use App\Mail\BaseMailer;

class ResetSuccessMail extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Successful Master Password Reset';
    const HEADER = 'Oriental Wallet - Successful Master Password Reset';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("We have successfully reset your master password. Please use the code below to access your Oriental Wallet services. For security purposes, we highly recommend that you change this temporary password immediately.")."</div><br/>";
        $this->message .= '<div><span class="verfication_code">' . $data->code . '</span></div><br /><br />';

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
