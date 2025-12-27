<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty,
            'price' => $this->price,
        ];
    }
}
