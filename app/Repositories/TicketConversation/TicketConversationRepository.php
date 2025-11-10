<?php

namespace App\Repositories\TicketConversation;

use App\TicketConversation;
use Illuminate\Support\Carbon;

class TicketConversationRepository implements TicketConversationInterface
{
    private $model;

    public function __construct(TicketConversation $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        $conversation = $this->model->create($request);
        if($conversation){
            return $conversation;
        }
        return false;
    }

    public function update($id, $request)
    {
        $conversation = $this->model->find($id);
        if ($conversation) {
            $conversation->update($request);
            return $conversation;
        }
        return false;
    }

    public function delete($id)
    {
        $conversation = $this->model->find($id);
        if ($conversation) {
            $conversation->delete();
            return $conversation;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getAll()
    {
        return $this->model->get();
    }
}
