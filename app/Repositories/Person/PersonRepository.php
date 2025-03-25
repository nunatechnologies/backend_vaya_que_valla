<?php

namespace App\Repositories\Person;

use App\Models\Person;
use App\Repositories\Person\PersonRepositoryInterface;

class PersonRepository implements PersonRepositoryInterface
{
    public function all()
    {
        return Person::all();
    }

    public function allquery()
    {
        return Person::query();
    }

    public function create(array $data)
    {
        return Person::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return Person::find($id);
    }
}
