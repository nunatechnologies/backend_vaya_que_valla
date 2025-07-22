<?php

namespace App\Services\User;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\PaginacionResource;
use App\Repositories\User\UserRepositoryInterface;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserById($id){
        return $this->isUserExists($id);
    }

    public function createUser($data)
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create($data);
            $user->assignRole($data['rol']);
            return $user;
        });
    }

    public function addRol($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->isUserExists($id);
            $user->roles()->detach();
            $user->assignRole($data['rol']);
            return $user->refresh();
        });
    }

    public function updateUser($id, $data)
    {
        return  $this->userRepository->update($id, $data);
    }

    public function findUserByEmail($email)
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user == null) {
            throw new \Exception(ErrorMessages::USER_NOT_FOUND, 404);
        }
        return $user;
    }

    private function isUserExists($userId)
    {
        return $this->userRepository->find($userId)
            ?? throw new \Exception(ErrorMessages::USER_NOT_FOUND, 404);
    }

    public function getAllUserpagination($datos)
    {
        $query = $this->userRepository->allquery();

        if ($datos->filled('search')) {
            $searchTerm = $datos->query('search');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($datos->query('role')) 
        {
            $role = $datos->query('role');
            $query->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            });
        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) 
        {
            $sortBy = $datos->query('sortBy');
            $orderBy = $datos->query('orderBy');
            $query->orderBy($sortBy, $orderBy);
        }

        $itemsPerPage = $datos->query('itemsPerPage') ?? 10;
        $page = $datos->query('page') ?? 1;
        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function getroles()
    {
        $roles = $this->userRepository->getAllRoles();
        return $roles;
    }
    public function selectAsesor(){
        return Auth::check() ? Auth::id() : $this->userRepository->getRandomAsesorId() ?? null;
    }
}
