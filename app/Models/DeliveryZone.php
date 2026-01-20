<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DeliveryZone extends Model
{
    use Auditable, HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'base_fee',
        'per_km_fee',
        'min_order_amount',
        'estimated_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_fee' => 'decimal:2',
            'per_km_fee' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'estimated_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($zone) {
            if (empty($zone->slug)) {
                $zone->slug = Str::slug($zone->name);
            }
        });
    }

    public function areas(): HasMany
    {
        return $this->hasMany(DeliveryZoneArea::class);
    }

    public function calculateDeliveryFee(float $distanceKm): float
    {
        return $this->base_fee + ($this->per_km_fee * $distanceKm);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
