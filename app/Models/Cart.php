<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'market_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->marketProduct->current_price;
        });
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->count();
    }

    public function getTotalQuantityAttribute(): float
    {
        return $this->items->sum('quantity');
    }

    public function addItem(int $marketProductId, float $quantity, ?string $notes = null): CartItem
    {
        $item = $this->items()->where('market_product_id', $marketProductId)->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->notes = $notes ?? $item->notes;
            $item->save();
            return $item;
        }

        return $this->items()->create([
            'market_product_id' => $marketProductId,
            'quantity' => $quantity,
            'notes' => $notes,
        ]);
    }

    public function updateItemQuantity(int $itemId, float $quantity): ?CartItem
    {
        $item = $this->items()->find($itemId);
        if ($item) {
            $item->quantity = $quantity;
            $item->save();
        }
        return $item;
    }

    public function removeItem(int $itemId): bool
    {
        return $this->items()->where('id', $itemId)->delete() > 0;
    }

    public function clear(): void
    {
        $this->items()->delete();
    }
}
