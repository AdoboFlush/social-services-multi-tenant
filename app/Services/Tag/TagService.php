<?php

namespace App\Services\Tag;

use App\Tag;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;

class TagService extends BaseService
{

    public function __construct()
    {
        
    }

    public function store(Request $request)
    {
        
        try{

            // Validation here

            $model = new Tag;
            $model->name = $request->name;
            $model->type = $request->type;
            if(!empty($request->parent_id)){
                $model->parent_id = $request->parent_id;
            }
            $model->custom_field = !empty($request->custom_field) ? $request->custom_field : '';
            $model->status = 1;
            $affected_rows = $model->save();
            // activity("Create Tag")
                    // ->causedBy(Auth::user())
                    // ->performedOn($model)
                    // ->withProperties($model)
                    // ->log('Create Tag : '.$model->name.' | '.$model->type); 
            return redirect('tags')->with('success', 'Record has been inserted!');

        }catch(Exception $e){
            report($e);
        }

        return redirect('tags')->with('error', 'Record insert failed');
        
    }

    public function update(Request $request)
    { 

        try{

            // Validation here
            $model = Tag::find($request->tag_id);
            if(!$model){
                return redirect('tags')->with('error', 'Record not found. Update failed');
            }
            $model->name = $request->name;
            $model->type = $request->type;
            if(!empty($request->parent_id)){
                $model->parent_id = $request->parent_id;
            }
            $model->custom_field = !empty($request->custom_field) ? $request->custom_field : '';
            $model->status = 1;
            $affected_rows = $model->save();
            // activity("Update Tag")
                // ->causedBy(Auth::user())
                // ->performedOn($model)
                // ->withProperties($model)
                // ->log('Update Tag : '.$model->name.' | '.$model->type); 
            return redirect('tags')->with('success', 'Record has been updated!');

        }catch(Exception $e){
            report($e);
        }

        return redirect('tags')->with('error', 'Record update failed');
        
    }

    public function get(string $type = '', int $parent_id = 0)
    {
        $model = new Tag;
        if(!empty($type)){
            $model = $model->where('type', $type);
        }
        $model = $model->where('parent_id', $parent_id);
        return $model->get();
    }

    
    public function getByParentName(string $parent_name)
    {
        $parent = Tag::where('name', $parent_name)->first();

        $model = new Tag;
        if($parent){
            $model = $model->where('parent_id', $parent->id);
        }else{
            return null;
        }
        
        return $model->get();
    }

    public function getByCustomField(string $type = '', array $custom_field_arr = [])
    {
        return Tag::when($type, fn($q) => $q->where("type", $type))
            ->when(!empty($custom_field_arr), fn($q) => $q->whereIn("custom_field", $custom_field_arr))
            ->get();
    }

    public function getById(Request $request)
    {   
        return Tag::find($request->id);
    }

    public function getAll(Request $request)
    {   
        $model = new Tag;
        if($request->has('search_type') && !empty($request->search_type)){
            $model = $model->where('type', $request->search_type);
        }
        $model = $this->buildDataTableFilter($model, $request, true, ['name', 'type']);
        $model = $this->buildModelQueryDataTable($model, $request);
        return $model->get();
    }

    public function getTags(string $type)
    {
        $model = new Tag;
        $model = $model->where('type', $type)->where('status', 1);
        return $model->pluck('name');
    }

    public function getTotalCount(Request $request)
    {   
        $model = new Tag;
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
                $model = Tag::find($selected_id);
                activity("Delete Tag")
                    ->causedBy(Auth::user())
                    ->performedOn($model)
                    ->withProperties($model)
                    ->log('Delete Tag ID : '.$selected_id); 
                $model->delete();
            }
            return 1;
        }
        return 0;
    }
}