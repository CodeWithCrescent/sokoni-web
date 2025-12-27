<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'base_fee' => $this->base_fee,
            'per_km_fee' => $this->per_km_fee,
            'min_order_amount' => $this->min_order_amount,
            'estimated_minutes' => $this->estimated_minutes,
            'is_active' => $this->is_active,
            'areas' => DeliveryZoneAreaResource::collection($this->whenLoaded('areas')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
