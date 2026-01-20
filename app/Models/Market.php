<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MarketCategory;

class Market extends Model
{
    use Auditable, HasFactory, SoftDeletes, HasUuids;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($market) {
            if (!$market->category_id) {
                $localMarketCategory = MarketCategory::where('slug', 'local-market')->first();
                if ($localMarketCategory) {
                    $market->category_id = $localMarketCategory->id;
                }
            }
            
            if (empty($market->slug)) {
                $market->slug = static::generateUniqueSlug($market->name);
            }
        });

        static::updating(function ($market) {
            if ($market->isDirty('name') && empty($market->slug)) {
                $market->slug = static::generateUniqueSlug($market->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'address',
        'latitude',
        'longitude',
        'min_order_amount',
        'photo',
        'cover_photo',
        'phone',
        'email',
        'operating_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'min_order_amount' => 'decimal:2',
            'operating_hours' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MarketCategory::class, 'category_id');
    }

    public function marketProducts(): HasMany
    {
        return $this->hasMany(MarketProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'market_products')
            ->withPivot(['price', 'stock', 'moq', 'is_available'])
            ->withTimestamps();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithActiveCategory($query)
    {
        return $query->whereHas('category', fn($q) => $q->active());
    }
}
