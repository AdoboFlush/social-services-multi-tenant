<?php

namespace App\Mail\User;

use App\Mail\BaseMailer;

class ResetPasswordRequestMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Reset Password';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message .= '<div>'. _lang('Please click the link below and enter the following confirmation code to update your password.') . '</div><br/>';
        $this->message .= '<div><p><span class="verfication_code">' . $data->verification_code . '</span></p></div><br/>';
        $this->message .= '<div><a href="'. $data->url  . '"class="btn btn-email">' . _lang('Reset Password') . '</a></div><br/>';
        $this->message .= '<div>' . _lang('If you received this notification by mistake, please ignore this email and your password will stay as it is.') . '</div>';

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');

    }
}
