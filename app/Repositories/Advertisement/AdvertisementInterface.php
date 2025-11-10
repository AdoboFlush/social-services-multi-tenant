<?php

namespace App\Repositories\Advertisement;

use App\Advertisement;
use App\Repositories\BaseInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdvertisementInterface extends BaseInterface
{
    public function getOrdenance(bool $omit_next_sequence = false, string $language = 'English'): array;

    /**
     * @return Advertisement|null
     */
    public function getBySequence(int $sequence, string $language = 'English');
    public function getNextSequence(string $language = 'English'): int;
    public function getAdvertisements(): LengthAwarePaginator;
    public function getBanners(string $language = 'English'): array;
}
