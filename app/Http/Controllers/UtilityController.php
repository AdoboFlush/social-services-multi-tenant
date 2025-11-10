<?php

namespace App\Http\Controllers;

use App\Setting;
use App\WelcomeMessage;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class UtilityController extends Controller
{
	public function __construct()
	{
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
	}

	public function payment_gateway(): View
	{
		return view('backend.administration.payment_gateway');
	}

	public function update_theme_option(Request $request)
    {
		foreach ($_POST as $key => $value) {
			 if($key == "_token"){
				 continue;
			 }

			 $data = array();
			 $data['value'] = is_array($value) ? serialize($value) : $value;
			 $data['updated_at'] = Carbon::now();
			 if(Setting::where('name', $key)->exists()){
				Setting::where('name','=',$key)->update($data);
			 }else{
				$data['name'] = $key;
				$data['created_at'] = Carbon::now();
				Setting::insert($data);
			 }
		}

		foreach($_FILES as $key => $value){
		   $this->upload_file($key,$request);
		}

		if(! $request->ajax()){
		   return back()->with('success', _lang('Saved successfully'));
		}else{
		   return response()->json(['result'=>'success','action'=>'update','message'=>_lang('Saved successfully')]);
		}
	}

	public function upload_logo(Request $request)
	{
		$this->validate($request, [
			'logo' => 'required|image|mimes:jpeg,png,jpg|max:8192',
		]);

		if ($request->hasFile('logo')) {
			$image = $request->file('logo');
			$name = 'logo.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/uploads');
			$image->move($destinationPath, $name);
			$data = array();
			$data['value'] = $name;

			if (Setting::where('name', "logo")->exists()) {
				Setting::where('name','=',"logo")->update($data);
			}else{
				$data['name'] = "logo";
				Setting::insert($data);
			}

            activity("Settings")
                ->causedBy(Auth::user())
                ->log('updated logo');

			if (!$request->ajax()) {
			   return redirect('admin/administration/general_settings')->with('success', _lang('Saved successfully'));
			}else{
			   return response()->json(['result'=>'success','action'=>'update','message'=>_lang('Logo Upload successfully')]);
			}
		}
	}

	public function upload_file($file_name, Request $request)
	{
		if ($request->hasFile($file_name)) {
			$file = $request->file($file_name);
			$name = 'file_'.time().".".$file->getClientOriginalExtension();
			$destinationPath = public_path('/uploads/media');
			$file->move($destinationPath, $name);

			$data = array();
			$data['value'] = $name;
			$data['updated_at'] = Carbon::now();

			if(Setting::where('name', $file_name)->exists()){
				Setting::where('name','=',$file_name)->update($data);
			}else{
				$data['name'] = $file_name;
				$data['created_at'] = Carbon::now();
				Setting::insert($data);
			}
		}
	}

	public function message_template()
	{
		return view('backend.administration.mesage_template');
	}

	public function backup_database()
	{
		/**
		 * this is not recommended...
		 */
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);

		$return = "";
		$database = 'Tables_in_'.DB::getDatabaseName();
		$tables = array();
		$result = DB::select("SHOW TABLES");

		foreach($result as $table){
			$tables[] = $table->$database;
		}

		foreach($tables as $table){
			$return .= "DROP TABLE IF EXISTS $table;";
			$result2 = DB::select("SHOW CREATE TABLE $table");
			$row2 = $result2[0]->{'Create Table'};
			$return .= "\n\n".$row2.";\n\n";
			$result = DB::select("SELECT * FROM $table");

			foreach($result as $row){
				$return .= "INSERT INTO $table VALUES(";
				foreach($row as $key=>$val){
					$return .= "'".addslashes($val)."'," ;
				}

				$return = substr_replace($return, "", -1);
				$return .= ");\n";
			}

			$return .= "\n\n\n";
		}

		//save file

		$file = 'backup/DB-BACKUP-'.time().'.sql';
		$handle = fopen($file,'w+');

		fwrite($handle,$return);
		fclose($handle);
        activity("Database backup")
            ->causedBy(Auth::user())
            ->log('created');

		return response()->download($file);

		return back()->with('success', _lang('Backup Created Successfully'));
	}
}
