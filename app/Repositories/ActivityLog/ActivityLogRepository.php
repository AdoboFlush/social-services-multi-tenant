<?php

namespace App\Repositories\ActivityLog;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class ActivityLogRepository implements ActivityLogInterface
{
    private $model;

    public function __construct(Activity $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->update($request);
            return $model;
        }
        return false;
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->delete();
            return $model;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getAll($request = null)
    {
        $activities = $this->model->orderBy('id','desc');

        if ($request && $request->has('filter')) {
            if (array_key_exists('search', $request->filter)) {
                $search = $request->filter['search'];
                $users = User::select('id')
                    ->where('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', '%' . $search . '%')
                    ->pluck('id');
                if ($users->count()) {
                    $activities->orWhereIn("causer_id", $users);
                    $activities->orWhere(function ($query) use ($users) {
                        $query->where("subject_type", "App\User")->whereIn("subject_id", $users);
                    });
                } else {
                    $activities->orWhere("log_name", 'LIKE', '%' . $search . '%');
                    $activities->orWhere("description", 'LIKE', '%' . $search . '%');
                }
            }

            $date_from = array_key_exists('date_from', $request->filter)
                ? Carbon::create($request->filter['date_from'])->startOfDay()
                : null;
            $date_to = array_key_exists('date_from', $request->filter)
                ? Carbon::create($request->filter['date_to'])->endOfDay()
                : null;

            if (array_key_exists('date_from', $request->filter) && array_key_exists('date_to', $request->filter)) {
                $activities->whereBetween('created_at', [
                    $date_from,
                    $date_to
                ]);
            } else {
                if (array_key_exists('date_from', $request->filter)) {
                    $activities->where('created_at', '>=', $date_from);
                }

                if (array_key_exists('date_to', $request->filter)) {
                    $activities->where('created_at', '<=', $date_to);
                }
            }
        }

        $request['per_page'] = 10;
        if (isset($request['per_page'])) {
            return $activities->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        return $activities->get();
    }
}
