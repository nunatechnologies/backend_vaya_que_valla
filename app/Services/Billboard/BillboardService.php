<?php

namespace App\Services\Billboard;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\Billboard\BillboardRepositoryInterface;

class BillboardService
{
    protected $billboardRepository;

    public function __construct(BillboardRepositoryInterface $billboardRepository)
    {
        $this->billboardRepository = $billboardRepository;
    }

    public function getBillboardById($id){
        return $this->isBillboardExists($id);
    }

    private function isBillboardExists($id)
    {
        return $this->billboardRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createBillboard($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->billboardRepository->create($data);
        });
    }

    public function updateBillboard($id, $data)
    {
        return $this->billboardRepository->update($id, $data);
    }

    public function getAllBillboardPagination($datos)
    {
        $query = $this->billboardRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('name', 'like', '%' . $datos->query('search') . '%')
				->orWhere('status', 'like', '%' . $datos->query('search') . '%')
				->orWhere('location', 'like', '%' . $datos->query('search') . '%')
				->orWhere('entity_status', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
