<?php

namespace App\Http\Resources\BillboardFace;

use App\Http\Resources\Billboard\BillboardResource;
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
            'face' => $this->face,
            'location_detail' => $this->location_detail,
            'billboard' => new BillboardResource($this->billboard)
        ];
    }
}
