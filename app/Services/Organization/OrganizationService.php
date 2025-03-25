<?php

namespace App\Services\Organization;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Repositories\Organization\OrganizationRepositoryInterface;

class OrganizationService
{
    protected $organizationRepository;

    public function __construct(OrganizationRepositoryInterface $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    public function getOrganizationById($id){
        return $this->isOrganizationExists($id);
    }

    private function isOrganizationExists($id)
    {
        return $this->organizationRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createOrganization($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->organizationRepository->create($data);
        });
    }

    public function updateOrganization($id, $data)
    {
        return $this->organizationRepository->update($id, $data);
    }

    public function getAllOrganizationPagination($datos)
    {
        $query = $this->organizationRepository->allquery();

        if ($datos->filled('search')) {
            $query->where('social_reason', 'like', '%' . $datos->query('search') . '%')
				->orWhere('name_contact', 'like', '%' . $datos->query('search') . '%')
				->orWhere('phone_contact', 'like', '%' . $datos->query('search') . '%');

        }

        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
