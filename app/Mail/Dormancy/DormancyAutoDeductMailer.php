<?php

namespace App\Mail\Dormancy;

use App\Mail\BaseMailer;

class DormancyAutoDeductMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Notice of Inactivity Fee';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $this->data = $data;
        $this->template = "mail.generic";
        $this->emailSubject = _lang(self::SUBJECT);
        $this->header = _lang(self::HEADER);
        $this->message .= '<div>' . _lang('An inactivity fee has been debited on your account due to 90 days of dormancy or no transaction for 90 days after your last completed transaction.') . '</<div><br/><br/>';
        $this->message .= '<table><tr><td>' . _lang('Date and Time')  . '</td><td>' . $data->transaction_date . '</td></tr>';
        foreach($data->result as $key => $value) {
            $this->message .= '<tr><td>' . _lang('Inactivity Fee') . '</td><td>'. $key. ' ' . formatAmount($value) . '</td></tr>';
        }
        $this->message .= '<tr><td>' . _lang('Reference Number')  . '</td><td>' . $data->transaction_number . '</td></tr>    
            </table><br/><br/>';
        $this->message .= '<div>'._lang('Please check your balance on your Oriental Wallet Account.') . '</div><br/>';
        $this->message .= '<div>'._lang('The details of this transaction can be viewed on your transaction history.') . '</div><br/>';

        $this->message .= '<div>'._lang('If you have any questions or concerns, please send us a message via ticket through your Oriental Wallet Account.') . '</div><br/>';
        $this->message .= '<div>'._lang('Thank you for your continued patronage. We are committed to providing our customers with high-quality service.') . '</div><br/>';

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
