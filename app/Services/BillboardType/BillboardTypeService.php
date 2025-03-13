<?php

namespace App\Services\BillboardType;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\BillboardType\BillboardTypeRepositoryInterface;

class BillboardTypeService
{
    protected $billboardTypeRepository;

    public function __construct(BillboardTypeRepositoryInterface $billboardTypeRepository)
    {
        $this->billboardTypeRepository = $billboardTypeRepository;
    }

    public function getBillboardTypeById($id){
        return $this->isBillboardTypeExists($id);
    }

    public function createBillboardType($data)
    {
        return DB::transaction(function () use ($data) {
            $billboard = $this->billboardTypeRepository->create($data);
            return $billboard;
        });
    }

    public function updateBillboardType($id, $data)
    {
        return  $this->billboardTypeRepository->update($id, $data);
    }

    private function isBillboardTypeExists($billboardTypeId)
    {
        return $this->billboardTypeRepository->find($billboardTypeId)
            ?? throw new \Exception(ErrorMessages::BILLBOARD_TYPE_NOT_FOUND, 404);
    }

    public function getAllBillboardTypePagination($datos)
    {
        $query = $this->billboardTypeRepository->allquery();

        if ($datos->filled('search')) {
            $searchTerm = $datos->query('search');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $sortBy = $datos->query('sortBy');
            $orderBy = $datos->query('orderBy');
            $query->orderBy($sortBy, $orderBy);
        }

        $itemsPerPage = $datos->query('itemsPerPage') ?? 10;
        $page = $datos->query('page') ?? 1;
        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }
}
