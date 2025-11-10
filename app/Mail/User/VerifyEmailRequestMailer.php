<?php


namespace App\Mail\User;

use App\Mail\BaseMailer;
use Illuminate\Http\Request;

class VerifyEmailRequestMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Change of Email Address Confirmation';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("Please click the link below to successfully change your email address, the link is valid for 24 hours.")."</div><br/>";
        $this->message .= '<div><a href="'. $data->url  . '" class="btn btn-email">' . _lang('Verify Email') . '</a></div><br /><br />';
        $this->message .= "<div>"._lang("If you did not request to change your e-mail address, please ignore this e-mail and your e-mail will stay as it is.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
