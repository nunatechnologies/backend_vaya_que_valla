<?php

namespace App\Repositories\BillboardFace;

interface BillboardFaceRepositoryInterface
{
    public function all();
    public function allquery();
    public function create(array $data);
    public function update($id, array $data);
    public function find($id);
}