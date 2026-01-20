<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use Auditable, HasFactory, SoftDeletes, HasUuids;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COLLECTING = 'collecting';
    const STATUS_COLLECTED = 'collected';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_number',
        'user_id',
        'market_id',
        'collector_id',
        'driver_id',
        'status',
        'subtotal',
        'delivery_fee',
        'service_fee',
        'discount',
        'total',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_phone',
        'delivery_instructions',
        'estimated_delivery_at',
        'confirmed_at',
        'collected_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'delivery_latitude' => 'decimal:8',
            'delivery_longitude' => 'decimal:8',
            'estimated_delivery_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'collected_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'AGZ';
        $date = now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}{$date}{$random}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'completed')->exists();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items()->sum('total_price');
        $this->total = $this->subtotal + $this->delivery_fee + $this->service_fee - $this->discount;
        $this->save();
    }

    public function updateStatus(string $newStatus, ?int $userId = null, ?string $notes = null): void
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;

        if ($newStatus === self::STATUS_CONFIRMED) {
            $this->confirmed_at = now();
        } elseif ($newStatus === self::STATUS_COLLECTED) {
            $this->collected_at = now();
        } elseif ($newStatus === self::STATUS_DELIVERED) {
            $this->delivered_at = now();
        } elseif ($newStatus === self::STATUS_CANCELLED) {
            $this->cancelled_at = now();
        }

        $this->save();

        $this->statusHistories()->create([
            'user_id' => $userId,
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'notes' => $notes,
        ]);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMarket($query, int $marketId)
    {
        return $query->where('market_id', $marketId);
    }

    public function scopeForCollector($query, int $collectorId)
    {
        return $query->where('collector_id', $collectorId);
    }

    public function scopeForDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getRouteKeyName()
    {
        return 'order_number';
    }
}
