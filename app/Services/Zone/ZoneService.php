<?php

namespace App\Services\Zone;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\Zone\ZoneRepositoryInterface;

class ZoneService
{
    protected $zoneRepository;

    public function __construct(ZoneRepositoryInterface $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    public function getZoneById($id){
        return $this->isZoneExists($id);
    }

    private function isZoneExists($id)
    {
        return $this->zoneRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createZone($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->zoneRepository->create($data);
        });
    }

    public function updateZone($id, $data)
    {
        return $this->zoneRepository->update($id, $data);
    }

    public function getAllZonePagination($datos)
    {
        $query = $this->zoneRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('name', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
