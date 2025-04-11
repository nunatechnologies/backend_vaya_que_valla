<?php

namespace App\Repositories\Request;

use App\Models\Request;
use App\Repositories\Request\RequestRepositoryInterface;

class RequestRepository implements RequestRepositoryInterface
{
    public function all()
    {
        return Request::all();
    }

    public function allquery()
    {
        return Request::query();
    }

    public function create(array $data)
    {
        return Request::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return Request::find($id);
    }
}
