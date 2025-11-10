<?php

namespace App\Http\Controllers;

use App\Services\Ticket\TicketFacade;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public $ticketFacade;

    public function __construct(TicketFacade $ticketFacade)
    {
        $this->ticketFacade = $ticketFacade;
    }

    public function index()
    {
        return $this->ticketFacade::index();
    }

    public function get(Request $request, $status)
    {
        return $this->ticketFacade::get($status, $request);
    }

    public function create(Request $request)
    {
        return $this->ticketFacade::create($request);
    }

    public function show($status = null, Request $request)
    {
        return $this->ticketFacade::show($status, $request);
    }

    public function showConversation($id)
    {
        return $this->ticketFacade::showConversation($id);
    }

    public function addMessage(Request $request, $id)
    {
        if($request->has("ticket_action") && $request->ticket_action == "update"){
            return $this->ticketFacade::updateTicket($request, $id);
        }
        return $this->ticketFacade::addMessage($request, $id);
    }

    public function archiveTicketsPast($days = 90)
    {
        return $this->ticketFacade::archiveTicketsPast($days);
    }

    public function viewCannedMessages()
    {
        return $this->ticketFacade::viewCannedMessages();
    }

    public function viewCannedMessage($id)
    {
        return $this->ticketFacade::viewCannedMessage($id);
    }

    public function updateOrCreateCannedMessage(Request $request, $id = null)
    {
        return $this->ticketFacade::updateOrCreateCannedMessage($request, $id);
    }

    public function deleteCannedMessage($id)
    {
        return $this->ticketFacade::deleteCannedMessage($id);
    }

    public function getCannedMessages($lang)
    {
        return $this->ticketFacade::getCannedMessages($lang);

    }
}
