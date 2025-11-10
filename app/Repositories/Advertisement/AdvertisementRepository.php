<?php

namespace App\Repositories\Advertisement;

use App\Advertisement;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvertisementRepository implements AdvertisementInterface
{
    private $model;

    public function __construct(Advertisement $model)
    {
        $this->model = $model;
    }

    public function create($request): Advertisement
    {
        return $this->model::create($request);
    }

    public function getAdvertisements(): LengthAwarePaginator
    {
        return $this->model::orderBy('sequence', 'ASC')->paginate();
    }

    public function getBanners(string $language = 'English'): array
    {
        return $this->model
            ->where('language', $language)
            ->orderBy('sequence')
            ->get()
            ->map(function ($advertisement) {
                return  [
                    'title' => $advertisement->title,
                    'banner_url' => $advertisement->banner_url,
                    'link' => $advertisement->link,
                ];
            })
            ->toArray();
    }

    public function getOrdenance(bool $omit_next_sequence = false, string $language = 'English'): array
    {
        $total_ads = $this->model::where('language', $language)->count();
        $next_sequence = $omit_next_sequence ? $total_ads : $total_ads + 1;
        $ordinals = collect();

        for ($i = 1; $i <= $next_sequence; $i++) {
            $ordinals->push([
                'sequence' => $i,
                'ordinal' => $this->model::toOrdinal($i),
            ]);
        }

        return  [
            'next_sequence' => $next_sequence,
            'ordinals' => $ordinals
        ];
    }

    public function getNextSequence(string $language = 'English'): int
    {
        return $this->model::where('language', $language)->count() + 1;
    }

    /**
     * @return Advertisement|null
     */
    public function getBySequence(int $sequence, string $language = 'English')
    {
        return $this->model::where('sequence', $sequence)
            ->where('language', $language)
            ->first();
    }

    public function delete($id): bool
    {
        $advertisement = $this->model::findOrFail($id);

        $advertisement->shiftSequence();

        return $advertisement->delete();
    }

    public function get($id)
    {

    }

    public function getAll()
    {

    }

    public function update($id, $request)
    {

    }
}
