<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'delivery_fee' => $this->delivery_fee,
            'service_fee' => $this->service_fee,
            'discount' => $this->discount,
            'total' => $this->total,
            'delivery_address' => $this->delivery_address,
            'delivery_latitude' => $this->delivery_latitude,
            'delivery_longitude' => $this->delivery_longitude,
            'delivery_phone' => $this->delivery_phone,
            'delivery_instructions' => $this->delivery_instructions,
            'estimated_delivery_at' => $this->estimated_delivery_at?->toISOString(),
            'confirmed_at' => $this->confirmed_at?->toISOString(),
            'collected_at' => $this->collected_at?->toISOString(),
            'delivered_at' => $this->delivered_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'cancellation_reason' => $this->cancellation_reason,
            'notes' => $this->notes,
            'is_paid' => $this->isPaid(),
            'can_be_cancelled' => $this->canBeCancelled(),
            'user' => new UserResource($this->whenLoaded('user')),
            'market' => new MarketResource($this->whenLoaded('market')),
            'collector' => new UserResource($this->whenLoaded('collector')),
            'driver' => new UserResource($this->whenLoaded('driver')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'latest_payment' => new PaymentResource($this->whenLoaded('latestPayment')),
            'status_histories' => OrderStatusHistoryResource::collection($this->whenLoaded('statusHistories')),
            'items_count' => $this->whenCounted('items'),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
