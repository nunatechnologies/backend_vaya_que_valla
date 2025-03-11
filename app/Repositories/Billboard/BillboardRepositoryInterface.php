<?php

namespace App\Repositories\Billboard;


interface BillboardRepositoryInterface
{
    public function all();
    public function allquery();

    public function create(array $data);

    public function update($id, array $data);

    public function find($id);
}
