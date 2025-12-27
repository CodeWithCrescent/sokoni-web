<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
            'unit_price' => $this->marketProduct->current_price ?? 0,
            'total_price' => $this->total_price,
            'market_product' => new MarketProductResource($this->whenLoaded('marketProduct')),
        ];
    }
}
