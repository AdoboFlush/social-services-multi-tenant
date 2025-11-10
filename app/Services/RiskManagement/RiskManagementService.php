<?php

namespace App\Services\RiskManagement;

use App\Repositories\Country\CountryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RiskManagementService extends BaseService
{
    private const LOGS_UPDATING = "UPDATING COUNTRY";

    public function __construct(CountryInterface $countryInterface)
    {
        $this->countryInterface = $countryInterface;
    }

    public function availableCountries(): View
    {
        $countries = $this->countryInterface->getAll();
        return view('backend.risk_management.countries', compact('countries'));
    }

    public function updateCountry(int $id, Request $request): JsonResponse
    {
        try {
            $param = $request->only("status");
            $param["status"] = $request->has("status") ? "active" : "inactive";
            $result = DB::transaction(function () use ($id, $param) {
                return $this->countryInterface->update($id, $param);
            });
            if ($result) {
                return response()->json(["result" => "success"]);
            }
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::error(self::LOGS_UPDATING . ' - ' . $message);
        }
        abort(500, "Error Occurred, Please try again !");
    }
}
