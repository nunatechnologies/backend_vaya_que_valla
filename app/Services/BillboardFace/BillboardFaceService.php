<?php

namespace App\Services\BillboardFace;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\BillboardFace\BillboardFaceRepositoryInterface;
use Illuminate\Contracts\Database\Eloquent\Builder;

class BillboardFaceService
{
    protected $billboardfaceRepository;

    public function __construct(BillboardFaceRepositoryInterface $billboardfaceRepository)
    {
        $this->billboardfaceRepository = $billboardfaceRepository;
    }

    public function getBillboardFaceById($id){
        return $this->isBillboardFaceExists($id);
    }

    private function isBillboardFaceExists($id)
    {
        return $this->billboardfaceRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createBillboardFace($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->billboardfaceRepository->create($data);
        });
    }

    public function updateBillboardFace($id, $data)
    {
        return $this->billboardfaceRepository->update($id, $data);
    }

    public function getAllBillboardFacePagination($datos)
    {
        $query = $this->billboardfaceRepository->allquery();

        if ($datos->filled('search')) {
            $search = $datos->query('search');
            $query->where('code', 'like', '%' . $datos->query('search') . '%')
				->orWhere('face', 'like', '%' . $datos->query('search') . '%')
				->orWhere('location_detail', 'like', '%' . $datos->query('search') . '%')
                ->orWhere('location', 'like', '%' . $datos->query('search') . '%')
                ->orWhere('name', 'like', '%' . $datos->query('search') . '%');

        }
        if ($datos->filled('city_id')) 
        {
            $cityId = $datos->query('city_id');
            $query->where('city_id', $cityId);
        }
        if ($datos->filled('face')) 
        {
            $face = $datos->query('face');
            $query->where('face', $face);
        }
        if ($datos->filled('ids')) {
            $ids = collect(explode(',', $datos->query('ids')))
                    ->filter(fn($id) => is_numeric($id))
                    ->map(fn($id) => (int) $id)
                    ->toArray();

            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }
        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
