<?php

namespace App\Repositories\Quote;

use App\Models\Quote;
use App\Repositories\Quote\QuoteRepositoryInterface;

class QuoteRepository implements QuoteRepositoryInterface
{
    public function all()
    {
        return Quote::all();
    }

    public function allquery()
    {
        return Quote::query();
    }

    public function create(array $data)
    {
        return Quote::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return Quote::find($id);
    }
}
