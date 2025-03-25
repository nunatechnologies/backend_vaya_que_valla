<?php

namespace App\Services\Person;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\Person\PersonRepositoryInterface;

class PersonService
{
    protected $personRepository;

    public function __construct(PersonRepositoryInterface $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    public function getPersonById($id){
        return $this->isPersonExists($id);
    }

    private function isPersonExists($id)
    {
        return $this->personRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createPerson($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->personRepository->create($data);
        });
    }

    public function updatePerson($id, $data)
    {
        return $this->personRepository->update($id, $data);
    }

    public function getAllPersonPagination($datos)
    {
        $query = $this->personRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('ci', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
