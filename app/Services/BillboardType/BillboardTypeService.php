<?php

namespace App\Services\BillboardType;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\BillboardType\BillboardTypeRepositoryInterface;

class BillboardTypeService
{
    protected $billboardtypeRepository;

    public function __construct(BillboardTypeRepositoryInterface $billboardtypeRepository)
    {
        $this->billboardtypeRepository = $billboardtypeRepository;
    }

    public function getBillboardTypeById($id){
        return $this->isBillboardTypeExists($id);
    }

    private function isBillboardTypeExists($id)
    {
        return $this->billboardtypeRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createBillboardType($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->billboardtypeRepository->create($data);
        });
    }

    public function updateBillboardType($id, $data)
    {
        return $this->billboardtypeRepository->update($id, $data);
    }

    public function getAllBillboardTypePagination($datos)
    {
        $query = $this->billboardtypeRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('name', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
