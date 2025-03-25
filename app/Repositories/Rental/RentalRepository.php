<?php

namespace App\Repositories\Rental;

use App\Models\Rental;
use App\Repositories\Rental\RentalRepositoryInterface;

class RentalRepository implements RentalRepositoryInterface
{
    public function all()
    {
        return Rental::all();
    }

    public function allquery()
    {
        return Rental::query();
    }

    public function create(array $data)
    {
        return Rental::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return Rental::find($id);
    }
}
