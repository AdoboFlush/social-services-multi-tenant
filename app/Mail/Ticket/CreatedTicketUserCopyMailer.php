<?php

namespace App\Mail\Ticket;

use App\Mail\BaseMailer;
use Carbon\Carbon;

class CreatedTicketUserCopyMailer extends BaseMailer
{
    const SUBJECT = '';
    const HEADER = '';
    const LINK_TEXT = '';

    public $content_top;
    public $content_bottom;

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

        $this->message = "<div>"._lang("Your ticket has been updated. To view the message, please login to your Oriental Wallet account.")."</div><br/>";

        $this->message .= "<div style='text-align: center'>";
        $this->message .= "<a style='background-color: #555555; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px;' href='".url("user/ticket/show/".$data->ticket_number)."'>". _lang("View Your Ticket")."</button>";
        $this->message .= "</div><br/><br/>";

        $this->signatureLink = env('APP_URL');
        $this->signatureEmail = env('MAIL_CONTACT');

        $this->content_top = true;
        $this->content_bottom = true;
    }
}
