<?php

namespace App\Repositories\DigitalBillboardPlan;

use App\Models\DigitalBillboardPlan;
use App\Repositories\DigitalBillboardPlan\DigitalBillboardPlanRepositoryInterface;

class DigitalBillboardPlanRepository implements DigitalBillboardPlanRepositoryInterface
{
    public function all()
    {
        return DigitalBillboardPlan::all();
    }

    public function allquery()
    {
        return DigitalBillboardPlan::query();
    }

    public function create(array $data)
    {
        return DigitalBillboardPlan::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return DigitalBillboardPlan::find($id);
    }
}
