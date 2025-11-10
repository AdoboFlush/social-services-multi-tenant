<?php

namespace App\Services\Setting;

use App\Maintenance;
use App\MaintenanceAffiliates;
use App\Repositories\Maintenance\MaintenanceInterface;
use App\Repositories\Setting\SettingInterface;
use App\Repositories\WelcomeMessage\WelcomeMessageInterface;
use App\Services\BaseService;
use App\WelcomeMessage;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use InvalidArgumentException;

class SettingService extends BaseService
{
    const LOGS_UPDATING = 'UPDATING SETTING';

    private $settingInterface;
    private $welcomeMessageInterface;
    private $maintenanceInterface;

    public function __construct(
        WelcomeMessageInterface $welcomeMessageInterface,
        SettingInterface $settingInterface,
        MaintenanceInterface $maintenanceInterface)
    {
        $this->welcomeMessageInterface = $welcomeMessageInterface;
        $this->settingInterface = $settingInterface;
        $this->maintenanceInterface = $maintenanceInterface;
    }

    public function index($category)
    {
        try {
            return view('backend.administration.settings.' . $category);
        } catch (\InvalidArgumentException $e){
            return redirect("admin/administration/settings/general");
        }
    }

    public function update($category, $request)
    {
        try{
            DB::beginTransaction();
            activity()->disableLogging();
            $old = array();
            $new = array();

            foreach ($request->except(['_token']) as $key => $value) {
                $value = isset($value) && !empty($value) ? $value : "";
                $setting = $this->settingInterface->getByKey($key);

                if ($setting) {
                    $old[$key] = $setting->value;
                    $this->settingInterface->update($setting->id,["name" => $key,"value" => $value]);
                } else {
                    $old[$key] = "";
                    $this->settingInterface->create(["name" => $key,"value" => $value]);
                }

                $new[$key] = $value;

                if ($old[$key] === $new[$key]) {
                    unset($old[$key]);
                    unset($new[$key]);
                }
            }

            $this->logUpdates($category, $old, $new);

            DB::commit();

            if (! $request->ajax()) {
                return back()->with('success', _lang('Saved successfully'));
            }else{
                return response()->json(['result'=>'success','action'=>'update','message'=>_lang('Saved successfully')]);
            }

        } catch (Exception $e) {

            DB::rollBack();

            $message = $this->getErrorMessage($e);

            Log::error(self::LOGS_UPDATING . ' - ' . $message);

            return response()->json([
                'success' => false,
                'message' => _lang("Error Occurred, Please try again !"),
            ]);
        }
    }

    private function logUpdates(string $category, $old, $new): void
    {
        activity()->enableLogging();
        activity(ucwords($category) . " Settings")
            ->causedBy(Auth::user())
            ->withProperties(['old' => $old, 'attributes' => $new])
            ->log('updated');
    }

    public function updateWelcomeMessage(Request $request): RedirectResponse
	{
		$result = back()->with('success', 'Welcome Message updated!');

		$validator = Validator::make($request->all(), [
			'content' => 'required|string',
        ]);

		if ($validator->fails()) {
			$result = back()
				->withErrors($validator)
				->withInput()
				->with('error', 'Unable to update Welcome message.');
		} else {
            $this->welcomeMessageInterface->updateContent($request);
		}

		return $result;
	}

	public function showWelcomeMessage(): View
	{
		$message = $this->welcomeMessageInterface->getWelcomeMessage();

		return view('backend.administration.welcome-message', [
			'message' => $message,
		]);
	}

    public function updateServiceMaintenance(Request $request): JsonResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $maintenanceID = $request->maintenance_id;
                $maintenance_data = Maintenance::find($maintenanceID);
                $maintenance_data->isMaintenance = isset($request->isMaintenance[$maintenanceID]) ? Maintenance::ACTIVE : 0;
                $maintenance_data->content = $request->content[$maintenanceID];
                $maintenance_data->jp_content = $request->jp_content[$maintenanceID];
                $maintenance_data->save();

                MaintenanceAffiliates::where('maintenance_id', $maintenanceID)->delete();
                
                $maintenance_affiliates_data['applies_to'] = $request['applies_to_'.$maintenanceID] ? $request['applies_to_'.$maintenanceID] : null;
                $maintenance_affiliates_data['exception'] = $request['exception_'.$maintenanceID] ? $request['exception_'.$maintenanceID] : null;

                if($maintenance_affiliates_data['applies_to']){
                    foreach ($maintenance_affiliates_data['applies_to'] as $affiliate_code) {
                        MaintenanceAffiliates::create([
                            'maintenance_id' => $maintenanceID,
                            'applies_to' => $affiliate_code,
                        ]);
                    }
                }
                
                if($maintenance_affiliates_data['exception']){
                    foreach ($maintenance_affiliates_data['exception'] as $affiliate_code) {
                        MaintenanceAffiliates::create([
                            'maintenance_id' => $maintenanceID,
                            'exception' => $affiliate_code,
                        ]);
                    }
                }
            });

            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Saved successfully.')]);;

        } catch (Exception $e) {
            report($e);
            return response()->json([
                'result' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
        
    }
}
