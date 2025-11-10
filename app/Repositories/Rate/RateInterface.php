<?php

namespace App\Repositories\Rate;

interface RateInterface
{
    public function create(array $param);
    public function fetch();
    public function truncate();
}
