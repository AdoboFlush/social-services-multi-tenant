<?php

namespace App\Repositories\CannedMessage;

use App\CannedMessage;

class CannedMessageRepository implements CannedMessageInterface
{
    private $model;

    public function __construct(CannedMessage $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        $cannedMessage = $this->model->create($request);
        if($cannedMessage){
            return $cannedMessage;
        }
        return false;
    }

    public function update($id, $request)
    {
        $cannedMessage = $this->model->find($id);
        if ($cannedMessage) {
            $cannedMessage->update($request);
            return $cannedMessage;
        }
        return false;
    }

    public function delete($id)
    {
        $cannedMessage = $this->model->find($id);
        if ($cannedMessage) {
            $cannedMessage->delete();
            return $cannedMessage;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getAll($where = [])
    {
        if($where){
            return $this->model->where($where)->get();
        }
        return $this->model->get();
    }
}
