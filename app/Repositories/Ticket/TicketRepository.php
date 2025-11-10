<?php

namespace App\Repositories\Ticket;

use App\Ticket;
use Illuminate\Support\Carbon;
use DB;
class TicketRepository implements TicketInterface
{
    private $model;

    const REOPENED = "re-opened";
    const ARCHIVED = "archived";

    public $dirty = [];

    public function __construct(Ticket $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        $request['id'] = $this->generateTicketId();
        $ticket = $this->model->create($request);
        if($ticket){
            return $ticket;
        }
        return false;
    }

    public function update($id, $request, $timestamps = true)
    {
        $ticket = $this->model->find($id);
        if ($ticket) {
            $ticket->timestamps = $timestamps;
            if($ticket->status == "solved" && $request["status"] == "new"){
                $request["status"] = self::REOPENED;
            }
            $ticket->fill($request);
            if($ticket->isDirty()){
                $this->setDirty($ticket->getDirty());
                $ticket->save();
            }
            return $ticket;
        }
        return false;
    }

    public function delete($id)
    {
        $ticket = $this->model->find($id);
        if ($ticket) {
            $ticket->delete();
            return $ticket;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getByTicketAndUserId($id,$user_id)
    {
        return $this->model->where(["user_id"=>$user_id])->find($id);
    }

    public function getAll($request = [],$paginate = true)
    {
        $per_page = 10;
        $orderBy = 'desc';


        if(isset($request['status']) && $request['status'] == "new"){
            $orderBy = 'asc';
        }

        if($request->has("filter") && $request->filter){
            $filters = $request->filter;
            $model = $this->model->with('user','operator','user_information','conversations')->orderBy('updated_at', $orderBy);
            if (array_key_exists('account_number', $request->filter)) {
                $model = $model->whereHas('user', function($query) use ($request) {
                    $query->where('account_number', '=', $request->filter['account_number']);
                });
                unset($filters['account_number']);
            }
            if (array_key_exists('country', $request->filter)) {
                $model = $model->whereHas('user_information', function($query) use ($request) {
                    $query->where('country_of_residence', '=', $request->filter['country']);
                });
                unset($filters['country']);
            }
            if (array_key_exists('operator', $request->filter)) {
                $model = $model->whereHas('operator', function ($query)  use ($request)  {
                    $query->where('first_name', 'LIKE', '%' . $request->filter['operator'] . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $request->filter['operator'] . '%')
                        ->orWhere(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', '%' . $request->filter['operator'] . '%');
                });
                unset($filters['operator']);
            }
            if (array_key_exists('date_created_from', $request->filter) && array_key_exists('date_created_to', $request->filter)) {
                $model = $model->whereBetween('created_at', [
                    $request->filter['date_created_from'] . ' 00:00:00',
                    $request->filter['date_created_to'] . ' 23:59:59'
                ]);
                unset($filters['date_created_from']);
                unset($filters['date_created_to']);
            } else {
                if (array_key_exists('date_created_from', $request->filter)) {
                    $model = $model->where('created_at', '>=', $request->filter['date_created_from'] . ' 00:00:00');
                    unset($filters['date_created_from']);
                }

                if (array_key_exists('date_created_to', $request->filter)) {
                    $model = $model->where('created_at', '<=', $request->filter['date_created_to'] . ' 23:59:59');
                    unset($filters['date_created_to']);
                }
            }
            if (array_key_exists('date_updated_from', $request->filter) && array_key_exists('date_updated_to', $request->filter)) {
                $model = $model->whereBetween('created_at', [
                    $request->filter['date_updated_from'] . ' 00:00:00',
                    $request->filter['date_updated_to'] . ' 23:59:59'
                ]);
                unset($filters['date_updated_from']);
                unset($filters['date_updated_to']);
            } else {
                if (array_key_exists('date_updated_from', $request->filter)) {
                    $model = $model->where('created_at', '>=', $request->filter['date_updated_from'] . ' 00:00:00');
                    unset($filters['date_created_from']);
                }

                if (array_key_exists('date_updated_to', $request->filter)) {
                    $model = $model->where('created_at', '<=', $request->filter['date_updated_to'] . ' 23:59:59');
                    unset($filters['date_updated_to']);
                }
            }
            $model = $model->where($filters);
        } else {
            if($request->has('status') && $request->status == "all"){
                $request->request->remove('status');
            }
            $model = $this->model->with('user','operator')->where($request->except("page"))->orderBy('updated_at', $orderBy);
        }

        if($paginate){
            return $model->paginate($per_page, ['*'], 'page', $request->page);
        }
        return $model->get();
    }

    public function getAllByUserId($sender_id, $param = [])
    {
        $where = ["user_id" => $sender_id];
        if(isset($param["status"]) && $param["status"]){
            $where["status"] = $param["status"];
        }

        $model = $this->model->where($where);
        $per_page = 10;
        $page = isset($param['page']) ? $param['page'] : 1;

        $model = $model->orderBy('updated_at', 'desc')->paginate($per_page, ['*'], 'page', $page);
        return $model;
    }

    public function archiveTicketsPast($days = 90)
    {
        $date = \Carbon\Carbon::today()->subDays($days);
        $this->model->where('updated_at','<',$date)->where("status","!=",self::ARCHIVED)->update(['status' => self::ARCHIVED]);
    }

    public function getDirty()
    {
        return $this->dirty;
    }

    public function setDirty($dirty)
    {
        $this->dirty = $dirty;
    }

    private function generateTicketId()
    {
        return "OWL" . Carbon::now()->format('Ymd') . "-" . mt_rand(10000000, 99999999);
    }
}
