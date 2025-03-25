<?php

namespace App\Services\Quote;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\Quote\QuoteRepositoryInterface;

class QuoteService
{
    protected $quoteRepository;

    public function __construct(QuoteRepositoryInterface $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    public function getQuoteById($id){
        return $this->isQuoteExists($id);
    }

    private function isQuoteExists($id)
    {
        return $this->quoteRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createQuote($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->quoteRepository->create($data);
        });
    }

    public function updateQuote($id, $data)
    {
        return $this->quoteRepository->update($id, $data);
    }

    public function getAllQuotePagination($datos)
    {
        $query = $this->quoteRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('status', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
