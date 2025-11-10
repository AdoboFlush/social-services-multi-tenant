<?php

namespace App\Mail\User;

use App\Mail\BaseMailer;

class CreatePasswordRequestMailer extends BaseMailer
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

        $this->message = "<div>"._lang("Please click the link below and enter the following confirmation code to create your password. This Link is valid for 24 hours. Your email will be confirmed once completed.")."</div><br/>";
        $this->message .= '<div><span class="verfication_code">' . $data->verification_code . '</span></div><br /><br />';
        $this->message .= '<div><a href="'. $data->url  . '"class="btn btn-email">' . _lang('Create Password') . '</a></div><br /><br />';

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
