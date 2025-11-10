<?php


namespace App\Mail\User;

use App\Mail\BaseMailer;
use Illuminate\Http\Request;

class VerifyPasswordRequestMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Completion of Change Password';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.content";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);

        $this->message = "<div>"._lang("Your password has been successfully changed.")."</div><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
