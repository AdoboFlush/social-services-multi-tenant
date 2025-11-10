<?php

namespace App\Services\Ticket;

use \Illuminate\Support\Facades\Facade;

class TicketFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Ticket\TicketService';
    }
}
