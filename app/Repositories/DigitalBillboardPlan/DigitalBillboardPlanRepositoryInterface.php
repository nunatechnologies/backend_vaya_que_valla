<?php

namespace App\Repositories\DigitalBillboardPlan;

interface DigitalBillboardPlanRepositoryInterface
{
    public function all();
    public function allquery();
    public function create(array $data);
    public function update($id, array $data);
    public function find($id);
}