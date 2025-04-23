<?php

namespace App\Http\Resources\Organization;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
            'user_id' => $this->user_id,
            'social_reason' => $this->social_reason,
            'nit' => $this->nit,
            'name_contact' => $this->name_contact,
            'phone_contact' => $this->phone_contact,
            'commision_percentage' => $this->commision_percentage,
        ];
    }
}
