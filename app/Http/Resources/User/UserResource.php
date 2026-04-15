<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Organization\OrganizationResource;
use App\Http\Resources\Person\PersonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'full_name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'cod_phone' => $this->cod_phone,
            'user_type' => $this->user_type,
            'roles' => $this->getRoleNames(),
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'avatar' => [
                'original' => $this->getFirstMediaUrl('default'),
                'md' => $this->getFirstMediaUrl('default','md'),
                'sm' => $this->getFirstMediaUrl('default','sm')
            ],
            'entity_status' => $this->entity_status,
            'person' => new PersonResource($this->whenLoaded('person')),
            'city_id' => $this->city_id,
            'city' => $this->whenLoaded('city', fn() => [
                'id' => $this->city->id,
                'name' => $this->city->name,
            ]),
        ];
    }
}
