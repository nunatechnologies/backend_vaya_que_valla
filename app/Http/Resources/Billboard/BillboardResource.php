<?php

namespace App\Http\Resources\Billboard;

use App\Http\Resources\BillboardType\BillboardTypeResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillboardResource extends JsonResource
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
            'entity_status' => $this->entity_status,
            'location' => $this->location,
            'size' => $this->size,
            'price_per_month' => $this->price_per_month,
            'status' => $this->status,
            'traffic_data' => $this->traffic_data,
            'image' => $this->image,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'billboard_type' => new BillboardTypeResource($this->billboardType),
            'city' => new CityResource($this->city),
            'advertiser' => new UserResource($this->advertiser)
        ];
    }
}
