<?php

namespace App\Services\Rental;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\Rental\RentalRepositoryInterface;

class RentalService
{
    protected $rentalRepository;

    public function __construct(RentalRepositoryInterface $rentalRepository)
    {
        $this->rentalRepository = $rentalRepository;
    }

    public function getRentalById($id){
        return $this->isRentalExists($id);
    }

    private function isRentalExists($id)
    {
        return $this->rentalRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createRental($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->rentalRepository->create($data);
        });
    }

    public function updateRental($id, $data)
    {
        return $this->rentalRepository->update($id, $data);
    }

    public function getAllRentalPagination($datos)
    {
        $query = $this->rentalRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('status', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
