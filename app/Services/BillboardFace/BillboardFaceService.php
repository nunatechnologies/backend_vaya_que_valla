<?php

namespace App\Services\BillboardFace;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\BillboardFace\BillboardFaceRepositoryInterface;

class BillboardFaceService
{
    protected $billboardFaceRepository;

    public function __construct(BillboardFaceRepositoryInterface $billboardFaceRepository)
    {
        $this->billboardFaceRepository = $billboardFaceRepository;
    }

    public function getBillboardFaceById($id){
        return $this->isBillboardFaceExists($id);
    }

    public function createBillboardFace($data)
    {
        return DB::transaction(function () use ($data) {
            $billboard = $this->billboardFaceRepository->create($data);
            return $billboard;
        });
    }

    public function updateBillboardFace($id, $data)
    {
        return  $this->billboardFaceRepository->update($id, $data);
    }

    private function isBillboardFaceExists($billboardFaceId)
    {
        return $this->billboardFaceRepository->find($billboardFaceId)
            ?? throw new \Exception(ErrorMessages::BILLBOARD_FACE_NOT_FOUND, 404);
    }

    public function getAllBillboardFacePagination($datos)
    {
        $query = $this->billboardFaceRepository->allquery();

        if ($datos->filled('search')) {
            $searchTerm = $datos->query('search');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('face', 'like', '%' . $searchTerm . '%')
                ->orWhere('location_detail', 'like', '%' . $searchTerm . '%');
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
