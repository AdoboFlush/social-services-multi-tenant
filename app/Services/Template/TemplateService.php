<?php

namespace App\Services\Template;

use App\Template;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TemplateService extends BaseService
{

    public function __construct()
    {
        
    }

    public function store(Request $request)
    {
        
        try{

            $validator = Validator::make($request->all(), [
                            'name' => 'required',
                            'properties_json' => 'required|json',
                            #'file_front' => 'image',
                            #'file_back' => 'image',
                        ]);
            
            if ($validator->fails()) {
                $message = _lang(($validator->errors()->first()));
                return back()->withInput()->with('error', $message);
            }

            $model = new Template;
            $model->name = $request->name;
            $model->remarks = $request->remarks;
            
            $file_front = $file_back = '';
            if($request->has('file_front')){
                $uploadedFile = $request->file('file_front');
                $file_front = time().'_'.$uploadedFile->getClientOriginalName();
                $uploadedFile->move(public_path('uploads/templates/'), $file_front);
            }
           
            if($request->has('file_back')){
                $uploadedFile = $request->file('file_back');
                $file_back = time().'_'.$uploadedFile->getClientOriginalName();
                $uploadedFile->move(public_path('uploads/templates/'), $file_back);
            }
            
            if($request->has('properties_json')){
                $properties = json_decode($request->properties_json, true);
                $properties['front']['bg']= !empty($file_front) ? $file_front : '';
                $properties['back']['bg'] = !empty($file_back) ? $file_back : '';
                $properties_json = json_encode($properties);
                $model->properties_json = $properties_json;
            }

            $model->allowed_user_create = $request->has('allowed_user_create');
            $model->allowed_user_update = $request->has('allowed_user_update');
            
            $affected_rows = $model->save();
            activity("Create Template")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Create Template : '.$model->name); 
            return back()->with('success', "New Template {$model->name} has been created");

        }catch(Exception $e){
            report($e);
        }

        return back()->with('error', 'Record insert failed');
        
    }

    public function update(Request $request)
    { 

        try{

            $model = Template::find($request->data_id);
            if(!$model){
                return back()->with('error', 'Record not found. Update failed');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'properties_json' => 'required|json',
            ]);

            if ($validator->fails()) {
                $message = _lang(($validator->errors()->first()));
                return back()->withInput()->with('error', $message);
            }
            
            $model->name = $request->name;
            $model->remarks = $request->remarks;

            $file_front = $file_back = '';
            if($request->hasFile('file_front')){
                $uploadedFile = $request->file('file_front');
                $file_front = time().'_'.$uploadedFile->getClientOriginalName();
                $uploadedFile->move(public_path('uploads/templates/'), $file_front);
            }

            if($request->hasFile('file_back')){
                $uploadedFile = $request->file('file_back');
                $file_back = time().'_'.$uploadedFile->getClientOriginalName();
                $uploadedFile->move(public_path('uploads/templates/'), $file_back);
            }

            if($request->has('properties_json')){
                $properties = json_decode($request->properties_json, true);
                $properties['front']['bg']= !empty($file_front) ? $file_front : $request->old_front_pic;
                $properties['back']['bg']= !empty($file_back) ? $file_back : $request->old_back_pic;
                $properties_json = json_encode($properties);
                $model->properties_json = $properties_json;
            }

            $model->allowed_user_create = $request->has('allowed_user_create');
            $model->allowed_user_update = $request->has('allowed_user_update');

            $affected_rows = $model->save();
            activity("Update Template")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Update Template : '.$model->name); 
            return back()->with('success', "Template {$model->name} has been updated");

        }catch(Exception $e){
            report($e);
        }

        return back()->with('error', 'Record Update failed');
        
    }

    public function get()
    {
        return Template::get();
    }

    public function getById(int $id)
    {   
        return Template::find($id);
    }

    public function getAll(Request $request)
    {   
        $model = new Template;
        if($request->has('search_type') && !empty($request->search_type)){
            $model = $model->where('type', $request->search_type);
        }
        $model = $this->buildDataTableFilter($model, $request, true, ['name', 'type']);
        $model = $this->buildModelQueryDataTable($model, $request);
        return $model->get();
    }

    public function getTemplates(string $type)
    {
        $model = new Template;
        $model = $model->where('type', $type)->where('status', 1);
        return $model->pluck('name');
    }

    public function getTotalCount(Request $request)
    {   
        $model = new Template;
        if($request->has('search_type') && !empty($request->search_type)){
            $model = $model->where('type', $request->search_type);
        }
        $model = $this->buildDataTableFilter($model, $request, true, ['name', 'type']);
        return $model->count();
    }

    public function deleteMultiple(Request $request)
    {
        if($request->has('selected_ids')){
            foreach($request->selected_ids as $selected_id){
                $model = Template::find($selected_id);
                activity("Delete Template")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Delete Template ID : '.$selected_id); 
                $model->delete();
            }
            return 1;
        }
        return 0;
    }
}