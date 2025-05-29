<?php

namespace App\Repositories\Category;

use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Category::all();
    }

    public function allquery()
    {
        return Category::query();
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update($id, array $data)
    {
        $object = $this->find($id);
        $object->update($data);
        return $object;
    }

    public function find($id)
    {
        return Category::find($id);
    }
}
