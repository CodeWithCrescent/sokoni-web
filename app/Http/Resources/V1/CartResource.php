<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'market' => new MarketResource($this->whenLoaded('market')),
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->items_count,
            'total_quantity' => $this->total_quantity,
            'subtotal' => $this->subtotal,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
