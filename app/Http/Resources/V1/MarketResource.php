<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'min_order_amount' => $this->min_order_amount,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'cover_photo' => $this->cover_photo ? asset('storage/' . $this->cover_photo) : null,
            'phone' => $this->phone,
            'email' => $this->email,
            'operating_hours' => $this->operating_hours,
            'is_active' => $this->is_active,
            'category' => new MarketCategoryResource($this->whenLoaded('category')),
            'products_count' => $this->whenCounted('products'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
