<?php

namespace App\Services\City;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\City\CityRepositoryInterface;

class CityService
{
    protected $cityRepository;

    public function __construct(CityRepositoryInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function getCityById($id){
        return $this->isCityExists($id);
    }

    private function isCityExists($id)
    {
        return $this->cityRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createCity($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->cityRepository->create($data);
        });
    }

    public function updateCity($id, $data)
    {
        return $this->cityRepository->update($id, $data);
    }

    public function getAllCityPagination($datos)
    {
        $query = $this->cityRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('department', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
