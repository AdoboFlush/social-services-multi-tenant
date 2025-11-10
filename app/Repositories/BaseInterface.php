<?php

namespace App\Repositories;

interface BaseInterface
{
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function get($id);
    public function getAll();
}
