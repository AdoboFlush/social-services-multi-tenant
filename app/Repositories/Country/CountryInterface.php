<?php

namespace App\Repositories\Country;

interface CountryInterface
{
    public function getAll();
    public function getAllActive();
    public function update(Int $id, Array $param);
}
