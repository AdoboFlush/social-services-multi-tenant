<?php

namespace App\Http\Controllers;

use App\Services\Note\NoteFacade;
use App\Services\Setting\SettingFacade;
use App\WelcomeMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private $settingFacade;

    public function __construct(
        SettingFacade $settingFacade,
        NoteFacade $noteFacade
    ){
        $this->settingFacade = $settingFacade;
        $this->noteFacade = $noteFacade;
    }

    /**
     * @return View | JsonResponse
    */
    public function index(string $category = "general", Request $request)
    {
        if($request->isMethod('get')){
            if($category === "notes"){
                return $this->noteFacade::index($category);
            }
            return $this->settingFacade::index($category);
        }

        if($category === "notes"){
            return $this->noteFacade::update($category, $request);
        }
        return $this->settingFacade::update($category, $request);
    }

    public function updateWelcomeMessage(Request $request, WelcomeMessage $message): RedirectResponse
    {
        return $this->settingFacade::updateWelcomeMessage($request, $message);
    }

    public function showWelcomeMessage(): View
    {
        return $this->settingFacade::showWelcomeMessage();
    }

    /**
     * @return View | JsonResponse
     */
    public function serviceMaintenance(Request $request)
    {
        if($request->isMethod('get')){
            return $this->settingFacade::getServiceMaintenance();
        }
        return $this->settingFacade::updateServiceMaintenance($request);
    }
}
