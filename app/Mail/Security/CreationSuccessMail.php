<?php

namespace App\Mail\Security;

use App\Mail\BaseMailer;

class CreationSuccessMail extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Successful Master Password Creation';
    const HEADER = 'Oriental Wallet - Successful Master Password Creation';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("You have successfully created your master password.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
