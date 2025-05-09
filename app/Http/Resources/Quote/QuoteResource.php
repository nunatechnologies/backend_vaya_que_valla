<?php

namespace App\Http\Resources\Quote;

use App\Http\Resources\BillboardFace\BillboardFaceResource;
use App\Http\Resources\DigitalBillboardPlan\DigitalBillboardPlanResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
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
            'billboard_face' => new BillboardFaceResource($this->billboardFace),
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_amount' => $this->total_amount,
            'months' => $this->months,
            'digital_billboard_plan' => new DigitalBillboardPlanResource($this->digitalBillboardPlan),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
