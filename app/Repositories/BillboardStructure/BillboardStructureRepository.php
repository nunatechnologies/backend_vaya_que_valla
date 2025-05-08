<?php

namespace App\Repositories\BillboardStructure;

use App\Models\BillboardStructure;
use App\Repositories\BillboardStructure\BillboardStructureRepositoryInterface;

class BillboardStructureRepository implements BillboardStructureRepositoryInterface
{
    public function all()
    {
        return BillboardStructure::all();
    }

    public function allquery()
    {
        return BillboardStructure::query();
    }

    public function create(array $data)
    {
        return BillboardStructure::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return BillboardStructure::find($id);
    }
}
