<?php

namespace App\Mail\TwoFactor;

use App\Mail\BaseMailer;

class TwoFactorForceUpdatePasswordMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - 2-Step Verification - Password Update';
    const HEADER = 'Oriental Wallet - 2-Step Verification - Password Update';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("To complete the password update, please enter the following verification code.")."</div><br/>";
        $this->message .= '<div><span class="verfication_code">' . $data->code . '</span></div><br /><br />';
        $this->message .= '<div>' . _lang('The code is valid for 15 minutes and can be used only once.').'</div><br />';

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
