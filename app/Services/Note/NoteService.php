<?php

namespace App\Services\Note;

use App\Repositories\Note\NoteInterface;
use App\Services\BaseService;
use App\Services\Setting\SettingFacade;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class NoteService extends BaseService
{
	const LOGS_UPDATING = 'UPDATING NOTE SETTING';

	public function __construct(
		NoteInterface $noteInterface,
		SettingFacade $settingFacade
	)
	{
		$this->noteInterface = $noteInterface;
		$this->settingFacade = $settingFacade;
	}

	public function index(string $category): View
	{
		try {
			return view('backend.administration.settings.' . $category, ['notes' => $this->getAll()]);
		} catch (\InvalidArgumentException $e) {
			return redirect("admin/administration/settings/notes");
		}
	}

	public function update(string $category, Request $request): JsonResponse
	{
		try {
			DB::beginTransaction();
			activity()->disableLogging();

			$previous_note = $this->noteInterface->get($request->note_id);
			
			$old['content'] = $previous_note->content;
			$old['jp_content'] = $previous_note->jp_content;
			
			$this->noteInterface->update($request->note_id, [
				"content" => $request->content[$request->note_id], 
				"jp_content" => $request->jp_content[$request->note_id],
			]);
			
			$new['content'] = $request->content[$request->note_id];
			$new['jp_content'] = $request->jp_content[$request->note_id];
			
			if ($old === $new) {
				return response()->json(['result' => 'error', 'action' => 'update', 'message' => _lang('No changes were made.')]);
			}

			$this->logUpdates($category, $old, $new);

			DB::commit();

			return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Saved successfully')]);

		} catch (Exception $e) {
			report($e);
			DB::rollBack();
			$message = $this->getErrorMessage($e);
			Log::error(self::LOGS_UPDATING . ' - ' . $message);

			return response()->json([
				'success' => false,
				'message' => _lang("Error Occurred, Please try again !"),
			]);
		}
	}

	public function getAll(): Collection
	{
		return $this->noteInterface->getAll();
	}

	public function logUpdates(string $category, $old, $new): void
	{
		 activity()->enableLogging();
		 activity(ucwords($category) . " Settings")
			  ->causedBy(Auth::id())
			  ->withProperties(['old' => $old, 'attributes' => $new])
			  ->log('updated');
	}
}
