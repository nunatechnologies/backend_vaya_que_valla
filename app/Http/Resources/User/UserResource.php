<?php

namespace App\Http\Resources\User;

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
            'profile_image' => $this->profile_image,
            'email' => $this->email,
            'position' => $this->position,
            'phone' => $this->phone,
            'cod_phone' => $this->cod_phone,
            'gender' => $this->gender,
            'entity_status' => $this->entity_status,
            //todo: poner los branch. un usuario tiene una sola branch

            //todo: que se vean los roles
            // 'roles'=> $this->getRoleNames(),
        ];
    }
}
