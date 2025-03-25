<?php

namespace App\Repositories\City;

use App\Models\City;
use App\Repositories\City\CityRepositoryInterface;

class CityRepository implements CityRepositoryInterface
{
    public function all()
    {
        return City::all();
    }

    public function allquery()
    {
        return City::query();
    }

    public function create(array $data)
    {
        return City::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return City::find($id);
    }
}
