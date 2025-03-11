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

    public function createBillboard($data)
    {
        return DB::transaction(function () use ($data) {
            $billboard = $this->billboardRepository->create($data);
            return $billboard;
        });
    }

    public function updateBillboard($id, $data)
    {
        return  $this->billboardRepository->update($id, $data);
    }

    private function isBillboardExists($billboardId)
    {
        return $this->billboardRepository->find($billboardId)
            ?? throw new \Exception(ErrorMessages::BILLBOARD_NOT_FOUND, 404);
    }

    public function getAllBillboardPagination($datos)
    {
        $query = $this->billboardRepository->allquery();

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
