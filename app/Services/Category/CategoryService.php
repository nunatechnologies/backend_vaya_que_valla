<?php

namespace App\Services\Category;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\Category\CategoryRepositoryInterface;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategoryById($id){
        return $this->isCategoryExists($id);
    }

    private function isCategoryExists($id)
    {
        return $this->categoryRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createCategory($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->categoryRepository->create($data);
        });
    }

    public function updateCategory($id, $data)
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function getAllCategoryPagination($datos)
    {
        $query = $this->categoryRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('name', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
