<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SEABankDeposit\SEABankDepositFacade;
use App\Services\DixonPay3DS\DixonPay3DSFacade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    public function __construct(
        SEABankDepositFacade $SEABankDeposit,
        DixonPay3DSFacade $DixonPay3DS
    ) {
        $this->SEABankDeposit = $SEABankDeposit;
        $this->DixonPay3DS = $DixonPay3DS;
    }

    public function payStagePostBackHandler(Request $request): Response
    {
        return $this->SEABankDeposit::payStagePostBackHandler($request);
    }

    public function payStageSuccessDepositPage(): RedirectResponse
    {
        return $this->SEABankDeposit::payStageSuccessDepositPage();
    }

    public function payStageErrorDepositPage(): RedirectResponse
    {
        return $this->SEABankDeposit::payStageErrorDepositPage();
    }

    public function dixonPay3DSNotificationHandler(Request $request) : Response
    {   
        return $this->DixonPay3DS::notificationHandler($request);
    }
    
}
