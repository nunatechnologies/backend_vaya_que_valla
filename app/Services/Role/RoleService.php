<?php

namespace App\Services\Role;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\Role\RoleRepositoryInterface;

class RoleService
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getRoleById($id){
        return $this->isRoleExists($id);
    }

    private function isRoleExists($id)
    {
        return $this->roleRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createRole($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->roleRepository->create($data);
        });
    }

    public function updateRole($id, $data)
    {
        return $this->roleRepository->update($id, $data);
    }

    public function getAllRolePagination($datos)
    {
        $query = $this->roleRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('name', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
