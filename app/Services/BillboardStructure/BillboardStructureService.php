<?php

namespace App\Services\BillboardStructure;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\BillboardStructure\BillboardStructureRepositoryInterface;

class BillboardStructureService
{
    protected $billboardstructureRepository;

    public function __construct(BillboardStructureRepositoryInterface $billboardstructureRepository)
    {
        $this->billboardstructureRepository = $billboardstructureRepository;
    }

    public function getBillboardStructureById($id){
        return $this->isBillboardStructureExists($id);
    }

    private function isBillboardStructureExists($id)
    {
        return $this->billboardstructureRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createBillboardStructure($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->billboardstructureRepository->create($data);
        });
    }

    public function updateBillboardStructure($id, $data)
    {
        return $this->billboardstructureRepository->update($id, $data);
    }

    public function getAllBillboardStructurePagination($datos)
    {
        $query = $this->billboardstructureRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('name', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
