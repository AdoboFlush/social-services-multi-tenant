<?php

namespace App\Repositories\Rate;

use App\Rate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

class RateRepository implements RateInterface
{
    private $model;

    public function __construct(Rate $model)
    {
        $this->model = $model;
    }

    public function create($param): Rate
    {
        return $this->model->create($param);
    }

    public function update($id, $request): Rate
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->update($request);
        }
        return $model;
    }

    public function delete($id): Rate
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->delete();
        }
        return $model;
    }

    public function get($id): Rate
    {
        return $this->model->find($id);
    }

    public function getAll(Request $request = null): Collection
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    public function fetch(): object
    {
        /**
         * @TODO will refactor this to accept both fixerio, and rapid forex api using curl
         */
        if (env('FOREX_API') === "fixerio") {
            $client = new GuzzleClient();
            $response = $client->request('GET', config('services.fixerio.baseurl'), [
                'query' => [
                    'access_key' => config('services.fixerio.key'),
                ]
            ]);
        } else { // FASTFOREX
            $this->httpClient = new GuzzleClient();
            $response = $this->httpClient->request('GET', env('FAST_FOREX_URL'), [
                'query' => [
                    'from' => Rate::BASE_CURRENCY, 
                    'api_key' => env('FAST_FOREX_KEY')
                ]
            ]);
        }
        return json_decode($response->getBody()->getContents());
    }

    public function truncate(): void
    {
        $this->model->truncate();
    }
}
