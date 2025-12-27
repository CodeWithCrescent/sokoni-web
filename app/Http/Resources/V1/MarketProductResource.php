<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'market_id' => $this->market_id,
            'product_id' => $this->product_id,
            'price' => $this->price,
            'stock' => $this->stock,
            'moq' => $this->moq,
            'is_available' => $this->is_available,
            'market' => new MarketResource($this->whenLoaded('market')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'bulk_prices' => MarketProductPriceResource::collection($this->whenLoaded('bulkPrices')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
