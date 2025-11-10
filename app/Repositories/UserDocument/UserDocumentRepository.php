<?php

namespace App\Repositories\UserDocument;

use App\Document;

class UserDocumentRepository implements UserDocumentInterface
{
    private $model;

    public function __construct(Document $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $document = $this->model->find($id);
        if ($document) {
            $document->update($request);
            return $document;
        }
        return false;
    }

    public function delete($id)
    {
        $document = $this->model->find($id);
        if ($document) {
            $document->delete();
            return $document;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getAll($filter = null)
    {
        $model = $this->model;
        return $model->get();
    }

    public function getAllByUserId($userId, $orderBy = "desc")
    {
        $model = $this->model->where('user_id', $userId);
        return $model->orderBy('created_at', $orderBy)->get();
    }

    public function updateStatus($id, $status)
    {
        $document = $this->model->find($id);
        if ($document && $document->status != "rejected" && $document->status != "approved") {
            $document->update(["status" => $status]);
            return $document;
        }
        return false;
    }

    public function updateAllByUserIdAndStatus($userId,$status,$param)
    {
        $documents = $this->model->where('user_id', $userId)->where('status',$status)->get();
        if($documents){
            foreach($documents as $document){
                if($param["status"] == "deleted"){
                    $document->delete();
                } else{
                    $document->status = $param["status"];
                    $document->save();
                }
            }
            return $documents;
        }
        return false;
    }
}