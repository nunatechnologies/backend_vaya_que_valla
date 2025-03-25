<?php

namespace App\Repositories\BillboardType;

interface BillboardTypeRepositoryInterface
{
    public function all();
    public function allquery();
    public function create(array $data);
    public function update($id, array $data);
    public function find($id);
}