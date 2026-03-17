<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'delivery_id',
        'order_date',
        'status',
        'total_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the delivery personnel that handles the order.
     */
    public function deliveryPersonnel(): BelongsTo
    {
        return $this->belongsTo(DeliveryPersonnel::class, 'delivery_id');
    }

    /**
     * Get the order details for the order.
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Get the products in this order.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_details')
            ->withPivot(['quantity', 'price']);
    }
}