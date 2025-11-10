<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Services\User\UserFacade;

class UserController extends BaseController
{
    
    public function __construct(UserFacade $user)
    {        
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function receive(Request $request)
    {
        return $this->depositJp::receive($request);        
        
    }

    public function checkDebitCardLimit(Request $request)
    {
        return $this->depositCard::checkDepositData($request);        
    }    
    
}