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

    public function getBillboardById($id){
        return $this->isCityExists($id);
    }

    public function createCity($data)
    {
        return DB::transaction(function () use ($data) {
            $city = $this->cityRepository->create($data);
            return $city;
        });
    }

    public function updateCity($id, $data)
    {
        return  $this->cityRepository->update($id, $data);
    }

    private function isCityExists($cityId)
    {
        return $this->cityRepository->find($cityId)
            ?? throw new \Exception(ErrorMessages::CITY_NOT_FOUND, 404);
    }

    public function getAllCityPagination($datos)
    {
        $query = $this->cityRepository->allquery();

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
