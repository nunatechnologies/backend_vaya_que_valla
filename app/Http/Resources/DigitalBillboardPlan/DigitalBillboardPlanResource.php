<?php

namespace App\Http\Resources\DigitalBillboardPlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DigitalBillboardPlanResource extends JsonResource
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
            'passes_per_hour' => $this->passes_per_hour,
            'price_per_month' => (float) $this->price_per_month,
            'seconds_per_day' => (int) $this->seconds_per_day,
            'max_videos' => (int) $this->max_videos,
            'description' => $this->description,
        ];
    }
}
