<?php

namespace App\Repositories\BillboardType;

use App\Models\BillboardType;
use App\Repositories\BillboardType\BillboardTypeRepositoryInterface;

class BillboardTypeRepository implements BillboardTypeRepositoryInterface
{
    public function all()
    {
        return BillboardType::all();
    }

    public function allquery()
    {
        return BillboardType::query();
    }

    public function create(array $data)
    {
        return BillboardType::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return BillboardType::find($id);
    }
}
