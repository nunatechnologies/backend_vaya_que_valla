<?php

namespace App\Http\Resources\BillboardFace;

use App\Http\Resources\Billboard\BillboardResource;
use App\Http\Resources\BillboardStructure\BillboardStructureResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Zone\ZoneResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillboardFaceResource extends JsonResource
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
            'code' => $this->code,
            'face' => $this->face,
            'location_detail' => $this->location_detail,
            'status' => $this->status,
            'approval_status' => $this->approval_status,
            'rented_from' => $this->rented_from,
            'available_from' => $this->available_from,
            'images' => [
                'original' => $this->getFirstMediaUrl('default'),
                'md' => $this->getFirstMediaUrl('default','md'),
                'sm' => $this->getFirstMediaUrl('default','sm')
            ],
            // 'billboard' => new BillboardResource($this->billboard)
            //Migrated from billboard
            'name' => $this->name,
            'entity_status' => $this->entity_status,
            'location' => $this->location,
            'zone' => new ZoneResource($this->zone),
            'size' => $this->size,
            'price_per_month' => $this->price_per_month,
            'traffic_data' => $this->traffic_data,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'billboard_structure' => new BillboardStructureResource($this->billboardStructure),
            'city' => new CityResource($this->city),
            'advertiser' => new UserResource($this->advertiser)
        ];
    }
}
