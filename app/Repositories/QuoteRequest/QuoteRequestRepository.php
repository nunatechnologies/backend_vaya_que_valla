<?php

namespace App\Repositories\QuoteRequest;

use App\Models\QuoteRequest;
use App\Repositories\QuoteRequest\QuoteRequestRepositoryInterface;

class QuoteRequestRepository implements QuoteRequestRepositoryInterface
{
    public function all()
    {
        return QuoteRequest::all();
    }

    public function allquery()
    {
        return QuoteRequest::query();
    }

    public function create(array $data)
    {
        return QuoteRequest::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return QuoteRequest::find($id);
    }
}
