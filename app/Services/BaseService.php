<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class BaseService
 */
abstract class BaseService
{
    /**
     * Illuminate\Http\Request object
     */
    protected $request;

    /**
     * Request setter
     *
     * @param Illuminate\Http\Request $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Request input getter
     *
     * @param $param Input key attribute
     * @return input value
     */
    public function input($param)
    {
        return Request::input($param);
    }

    public function sendError($error, $errorMessages = [])
    {
        $response = [
            'success' => false,
            'message' => $error['message'],
            'error_code'=> $error['code'],
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $error['http_code']);
    }

    protected function getErrorMessage($e)
    {
        return isset($e->message)? $e->message : $e->getMessage();
    }

    public function sendResponse($result, $message, $headers = [])
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $result,
        ];
        return response()->json($response, 200, $headers);
    }

    public function buildDataTableFilter(object $model, Request $request, bool $allSearch = false, 
                                            array $allSearchFields = [], array $replaceFieldName = [], array $relationshipSearch = [])
    {
        if($request->has('columns')){
            foreach($request->columns as $column){
                if($column['searchable'] == 'true'){
                    if(isset($column['search']['value'])){
                        if(!empty($column['search']['value'])){
                            if(in_array($column['data'], $relationshipSearch)){
                                $fieldFrag = explode('.', $column['data']);
                                $relationship = $fieldFrag[0];
                                $relationship_field_to_search = $fieldFrag[1];
                                $model = $model->whereHas($relationship, function($q) use ($column, $relationship_field_to_search){
                                    if($relationship_field_to_search == 'full_name'){
                                        $q->orWhere('first_name', 'like', '%'.$column['search']['value'].'%');
                                        $q->orWhere('last_name', 'like', '%'.$column['search']['value'].'%');
                                    }else{
                                        $q->where($relationship_field_to_search, 'like', '%'.$column['search']['value'].'%');
                                    }
                                });
                            }else{
                                $fieldSearch = isset($replaceFieldName[$column['data']]) ? $replaceFieldName[$column['data']]  : $column['data']; 
								if($fieldSearch === "requestor_full_name") {
									$model = $model->where(DB::raw("CONCAT(requestor_last_name,', ',requestor_first_name)"), 'like', '%'.$column['search']['value'].'%');  
								} else {
									$model = $model->where($fieldSearch, 'like', '%'.$column['search']['value'].'%');
								}
                            }
                        }
                    }
                }
            }
        }
        if($request->has('search') && $allSearch === true){
            if(!empty($request->search['value'])){
                $model->where(function($q) use ($allSearchFields, $request) {
                    foreach($allSearchFields as $allSearchField){
                        $q->orWhere($allSearchField, 'like', '%'.$request->search['value'].'%');
                    }
                });
            }
        }
        return $model;
    }

    public function buildModelQueryDataTable(object $model, Request $request, array $replaceFieldName = [])
    { 
        if($request->has('order')){
            if($request->has('columns')){
                if($request['columns'][$request['order'][0]['column']]['orderable'] == 'true'){
                    $order_field = $request['columns'][$request['order'][0]['column']]['data'];
                    $fieldOrder = isset($replaceFieldName[$order_field]) ? $replaceFieldName[$order_field]  : $order_field; 
                    $order_dir = $request['order'][0]['dir'];
                    $model  = $model->orderBy( $fieldOrder, $order_dir );
                }
            }
        }
        if($request->has('start') && $request->has('length')){
            $model = $model->offset($request->start)->limit($request->length);
        }else{
            $model = $model->limit(10);
        }
        return $model;
    }

}
