<?php

namespace App\Http\Resources\Request;

use App\Http\Resources\Quote\QuoteResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'user' => new UserResource($this->user),
            'company' => $this->company,
            'quotes' => QuoteResource::collection($this->quotes),
            'status' => $this->status,
            'description' => $this->description,
            'budget_description' => $this->budget_description,
            'tentative_start_date' => $this->tentative_start_date,
            'files' => $this->getMedia()->map(function ($media) {
                return $media->getFullUrl(); // o ->getUrl() si usás local disk
            })->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
