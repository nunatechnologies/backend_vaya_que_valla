<?php

namespace App\Http\Resources\Zone;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
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
            'images' => [
                'original' => $this->getFirstMediaUrl('default'),
                'md' => $this->getFirstMediaUrl('default','md'),
                'sm' => $this->getFirstMediaUrl('default','sm')
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
