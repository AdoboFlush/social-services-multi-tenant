<?php

namespace App\Mail\Ticket;

use App\Mail\BaseMailer;
use Carbon\Carbon;

class CreatedTicketAdminMailer extends BaseMailer
{
    const SUBJECT = '';
    const HEADER = '';
    const LINK_TEXT = '';

    public function __construct($data)
    {
        parent::__construct('');

        $info = array(
            "ticket_number" => $data->ticket_number,
            "account_number" => $data->account_number,
            "account_name" => $data->first_name ." ". $data->last_name,
        );
        $this->data = $data;
        $this->template = "mail.ticket";
        $this->emailSubject = _lang("Oriental Wallet - [{ticket_number}] - {account_number} {account_name}",$info);
        $this->header = _lang(self::HEADER);

        $this->message .= "<div>"._lang("This is your response to the following client.")."</div><br/>";

        $this->message .= "<div>";
        $this->message .= _lang("Email Address:")." ". $data->email ."<br/>";
        $this->message .= _lang("Oriental Wallet Account No:")." ". $data->account_number ."<br/>";
        $this->message .= _lang("Name:")." ". $data->first_name ." ". $data->last_name ."<br/>";
        $this->message .= _lang("Subject:")." ". $data->subject ."<br/>";
        $this->message .= _lang("Message:")." ". $data->message ."<br/>";
        if(isset($data->attachments) && $data->attachments){
            $this->message .= _lang("Attachment/s:");
            foreach($data->attachments as $key => $value){
                $this->message .= "<br/>";
                $this->message .= "<a href='". $value ."' target='_blank'>".$key."</a>";
            }
        }
        $this->message .= "</div><br/><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');
    }
}
