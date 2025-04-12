<?php

namespace App\Services\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\Request\RequestRepositoryInterface;

class RequestService
{
    protected $requestRepository;

    public function __construct(RequestRepositoryInterface $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    public function getRequestById($id){
        return $this->isRequestExists($id);
    }

    private function isRequestExists($id)
    {
        return $this->requestRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createRequest($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->requestRepository->create($data);
        });
    }

    public function updateRequest($id, $data)
    {
        return $this->requestRepository->update($id, $data);
    }

    public function getAllRequestPagination($datos)
    {
        $query = $this->requestRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('company', 'like', '%' . $datos->query('search') . '%')
				->orWhere('user_id', 'like', '%' . $datos->query('search') . '%');
        }

        if ($datos->filled('user_id')) {
            $query->where('user_id', $datos->query('user_id'));
        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
