<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketProduct extends Model
{
    use Auditable, HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'market_id',
        'product_id',
        'price',
        'stock',
        'moq',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'moq' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function bulkPrices(): HasMany
    {
        return $this->hasMany(MarketProductPrice::class)->orderBy('min_qty');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    public function getPriceForQuantity(int $quantity): float
    {
        $bulkPrice = $this->bulkPrices()
            ->where('min_qty', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->whereNull('max_qty')
                    ->orWhere('max_qty', '>=', $quantity);
            })
            ->orderBy('min_qty', 'desc')
            ->first();

        return $bulkPrice ? (float) $bulkPrice->price : (float) $this->price;
    }
}
