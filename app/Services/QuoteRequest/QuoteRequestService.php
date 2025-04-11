<?php

namespace App\Services\QuoteRequest;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\QuoteRequest\QuoteRequestRepositoryInterface;

class QuoteRequestService
{
    protected $quoterequestRepository;

    public function __construct(QuoteRequestRepositoryInterface $quoterequestRepository)
    {
        $this->quoterequestRepository = $quoterequestRepository;
    }

    public function getQuoteRequestById($id){
        return $this->isQuoteRequestExists($id);
    }

    private function isQuoteRequestExists($id)
    {
        return $this->quoterequestRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createQuoteRequest($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->quoterequestRepository->create($data);
        });
    }

    public function updateQuoteRequest($id, $data)
    {
        return $this->quoterequestRepository->update($id, $data);
    }

    public function getAllQuoteRequestPagination($datos)
    {
        $query = $this->quoterequestRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('request_id', 'like', '%' . $datos->query('search') . '%')
				->orWhere('quote_id', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
