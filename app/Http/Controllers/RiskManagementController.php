<?php

namespace App\Http\Controllers;

use App\Country;
use App\Services\RiskManagement\RiskManagementFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiskManagementController extends Controller
{
    public function __construct(RiskManagementFacade $riskManagementFacade)
    {
        $this->riskManagementFacade = $riskManagementFacade;
    }

    public function availableCountries(): View
    {
        return $this->riskManagementFacade::availableCountries();
    }

    public function updateCountry(Request $request, Country $country): JsonResponse
    {
        return $this->riskManagementFacade::updateCountry($country->id, $request);
    }
}
