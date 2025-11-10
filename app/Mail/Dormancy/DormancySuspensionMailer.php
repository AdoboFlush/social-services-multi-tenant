<?php

namespace App\Mail\Dormancy;

use App\Mail\BaseMailer;

class DormancySuspensionMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Account Suspension Notice';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');
        $this->data = $data;
        $this->template = "mail.generic";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);
        $this->message .= '<br><div>' . _lang('Your account has been temporarily suspended due to 90 days inactivity since your account was created, or 90 days inactivity after the last transaction was completed. While your account is suspended, you can still log in and receive deposits in your Oriental Wallet account. To avoid account suspension in the future, make a transaction at least once every 3 months (90 days).') . '</div><br>';
        $this->message .= '<div>'._lang('"Transactions" are monetary-related activities such as deposit, withdrawal, internal transfer, currency exchange, etc. Logging into your account, checking your history, updating your profile, are not considered as transactions. For more details, you may visit our Terms & Condition through https://orientalwallet.com/terms-and-conditions/.') . '</div><br>';
        $this->message .= '<div>'._lang('If you wish to reactivate your account, please send us a message via ticket through your Oriental Wallet Account and we will provide you the necessary steps for the account reactivation.') . '</div><br>';
        $this->message .= '<div>'._lang('Thank you for understanding and for your cooperation.') . '</div><br>';

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
