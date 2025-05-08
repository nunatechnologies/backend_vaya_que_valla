<?php

namespace App\Services\DigitalBillboardPlan;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\DigitalBillboardPlan\DigitalBillboardPlanRepositoryInterface;

class DigitalBillboardPlanService
{
    protected $digitalbillboardplanRepository;

    public function __construct(DigitalBillboardPlanRepositoryInterface $digitalbillboardplanRepository)
    {
        $this->digitalbillboardplanRepository = $digitalbillboardplanRepository;
    }

    public function getDigitalBillboardPlanById($id){
        return $this->isDigitalBillboardPlanExists($id);
    }

    private function isDigitalBillboardPlanExists($id)
    {
        return $this->digitalbillboardplanRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createDigitalBillboardPlan($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->digitalbillboardplanRepository->create($data);
        });
    }

    public function updateDigitalBillboardPlan($id, $data)
    {
        return $this->digitalbillboardplanRepository->update($id, $data);
    }

    public function getAllDigitalBillboardPlanPagination($datos)
    {
        $query = $this->digitalbillboardplanRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('name', 'like', '%' . $datos->query('search') . '%')
				->orWhere('passes_per_hour', 'like', '%' . $datos->query('search') . '%')
				->orWhere('description', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
