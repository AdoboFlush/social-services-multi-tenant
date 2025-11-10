<?php

namespace App\Mail\User;

use App\Mail\BaseMailer;
use Auth;

class UpdateInformationRequestMailer extends BaseMailer
{
    const SUBJECT = 'Oriental Wallet - Request to Update User Information';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $name = ucfirst($data->old_first_name) . " " . ucfirst($data->old_last_name)    ;
        $account_number = $data->account_number;


        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER . $name;
        $this->message = '<style>
                          table, th, td {
                              border:none !important;
                            }
                         </style>';
        $this->message .= '<table style="table" border="0" cellpadding="0" height="100" width="100%">';
        $this->message .= '<tr><td align="center" valign="top" class="email-container">';
        $this->message .= '<table border="0" cellpadding="0" cellspacing="0" width="600">';

        $this->message .= '<tr><td>Dear Customer Support, <br /><br /></td></tr>';
        $this->message .= '<tr><td><b>'. $name . ' '. $account_number .'</b> is requesting to update the following details. <br /><br /></td></tr>';

        $this->message .= '<tr><td><b>Member Information:</b></td><td></td></tr>';
        $this->message .= '<tr><td>Email Address </td><td>'. Auth::user()->email .'</td></tr>';
        $this->message .= '<tr><td>Oriental Wallet Account Number </td><td>'. $account_number.'</td></tr>';
        $this->message .= '<tr><td>Name </td><td>'. $name .'</td></tr>';
        $this->message .= '<tr><td>Date of Birth: </td><td>'. Auth::user()->user_information->date_of_birth  .'</td></tr>';

        $this->message .= '<tr><td><b>Update the following to:</b></td><td></td></tr>';
        if (isset($data['first_name'])  && $data['first_name'] != '') {
            $this->message .= '<tr><td>First Name</td><td>'. $data['first_name'] .'</td></tr>';
        }

        if (isset($data['last_name'])  && $data['last_name'] != '') {
            $this->message .= '<tr><td>Last Name</td><td>'. $data['last_name'] .'</td></tr>';
        }

        if(isset($data['date_of_birth']) && $data['date_of_birth'] != '') {
            $this->message .= '<tr><td>Data of Birth</td><td>'. $data['date_of_birth'] .'</td></tr>';
        }

        $this->message .= '</td>
                                </tr>
                            </table> </table>';
        $this->message .= '<br/>';
        $this->message .= '<br/><br/>';

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
