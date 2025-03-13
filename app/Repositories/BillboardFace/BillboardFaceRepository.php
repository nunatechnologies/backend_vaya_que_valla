<?php

namespace App\Repositories\BillboardFace;

use App\Models\BillboardFace;
use App\Repositories\BillboardFace\BillboardFaceRepositoryInterface;

class BillboardFaceRepository implements BillboardFaceRepositoryInterface
{
    public function all()
    {
        return BillboardFace::all();
    }

    public function allquery()
    {
        return BillboardFace::query();
    }

    public function create(array $data)
    {
        return BillboardFace::create($data);
    }

    public function update($id, array $data)
    {
        $billboardFace = $this->find($id);
        $billboardFace->update($data);
        return $billboardFace;
    }

    public function find($id)
    {
        return BillboardFace::find($id);
    }
}
