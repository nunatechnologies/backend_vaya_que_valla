<?php

namespace App\Repositories\Organization;

use App\Models\Organization;
use App\Repositories\Organization\OrganizationRepositoryInterface;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    public function all()
    {
        return Organization::all();
    }

    public function allquery()
    {
        return Organization::query();
    }

    public function create(array $data)
    {
        return Organization::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return Organization::find($id);
    }
}
