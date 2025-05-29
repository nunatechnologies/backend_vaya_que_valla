<?php

namespace App\Repositories\Zone;

use App\Models\Zone;
use App\Repositories\Zone\ZoneRepositoryInterface;

class ZoneRepository implements ZoneRepositoryInterface
{
    public function all()
    {
        return Zone::all();
    }

    public function allquery()
    {
        return Zone::query();
    }

    public function create(array $data)
    {
        return Zone::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return Zone::find($id);
    }
}
