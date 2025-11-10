<?php

namespace App\Repositories\CardTopUp;

use App\CardTopUp;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;

class CardTopUpRepository implements CardTopUpInterface
{
    private $model;

    public function __construct(CardTopUp $model)
    {
        $this->model = $model;
    }

    public function create($params)
    {
        return $this->model->create($params);
    }

    public function get($id)
    {
        return $this->model->with('user_updated')->find($id);
    }

    public function getAll()
    {
        $model = $this->model->with('internal_transfer')->orderBy('created_at', 'desc');
        return $model->get();
    }

    public function getAllApplying()
    {
        $model = $this->model->whereNull('status')
            ->orWhere('status', 'applying');
        return $model->get();
    }

    public function getAllList($request)
    {
        $model = $this->model->with(['internal_transfer', 'transaction', 'user', 'transaction.created_user']);

        if (!is_null($request)) {
            if (!empty($request->date_from) && !empty($request->date_to)) {
               $model = $model->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to. ' 23:59:59'
                ]);
            } else {
                if (!empty($request->date_from)) {
                    $model = $model->where('created_at', '>=', $request->date_from . ' 00:00:00');
                }

                if (!empty($request->date_to)) {
                    $model = $model->where('created_at', '<=', $request->date_to . ' 23:59:59');
                }
            }

            if (!empty($request->status)) {
                 $model = $model->where('status', $request->status);
            }
            return $model->orderBy('created_at', 'desc')->get();
        }

        return $model->orderBy('created_at', 'desc')->get();
    }

    public function update($id, $request)
    {
        $topup = $this->model->find($id);
        if ($topup) {
            $topup->update($request);
            return $topup;
        }
        return false;
    }

    public function delete($id)
    {
        $topup = $this->model->find($id);
        if ($topup) {
            $topup->delete();
            return $topup;
        }
        return false;
    }

    public function getDailyLimit($user_id)
    {
        $total = $this->model->where('user_id', $user_id)
            ->whereDate('created_at', Carbon::today())
            ->where(function($query){
                $query->where('status', CardTopUp::COMPLETED)
                    ->orWhere('status', CardTopUp::APPLYING);
            })->sum('amount');
        return $total;
    }

    public function getMonthyLimit($user_id)
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $total = $this->model->where('user_id', $user_id)
            ->whereBetween('created_at', [$start, $end])
            ->where(function($query){
                $query->where('status', CardTopUp::COMPLETED)
                    ->orWhere('status', CardTopUp::APPLYING);
            })->sum('amount');
        return $total;
    }

    public function sendDepositRequest($payload)
    {
        try {
            Log::info('SEND DEPOSIT REQUEST API: ' . json_encode($payload));
            $httpClient = new GuzzleClient([
                'headers' => [
                    'Accept' => 'application/json'
                 ]
            ]);
            $response = $httpClient->post(env('CARD_TOP_API'), [
                'form_params' => [
                    'sid' => env('CARD_TOP_SID'),
                    'token' => env('CARD_TOP_TOKEN'),
                    'uid' => $payload->uid,
                    'am' => $payload->am,
                    'oid' => $payload->oid
                ]
            ]);

            Log::info('SEND DEPOSIT RESPONSE API: ' . json_encode($response));
            return $response;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return false;
        }
    }

}
