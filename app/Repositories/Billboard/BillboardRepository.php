<?php

namespace App\Repositories\Billboard;

use App\Models\Billboard;
use App\Repositories\Billboard\BillboardRepositoryInterface;

class BillboardRepository implements BillboardRepositoryInterface
{
    public function all()
    {
        return Billboard::all();
    }

    public function allquery()
    {
        return Billboard::query();
    }

    public function create(array $data)
    {
        return Billboard::create($data);
    }

    public function update($id, array $data)
    {
        $billboard = $this->find($id);
        $billboard->update($data);
        return $billboard;
    }

    public function find($id)
    {
        return Billboard::find($id);
    }
}
