<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Default Role to Customer for newly registered users
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->role_id)) {
                $customerRole = Role::where('name', 'customer')->first();
                if ($customerRole) {
                    $user->role_id = $customerRole->id;
                }
            }
        });
    }

    /**
     * Get the role associated with the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the customer profile associated with the user.
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id');
    }

    /**
     * Get the market profile associated with the user.
     */
    public function market(): HasOne
    {
        return $this->hasOne(Market::class, 'id', 'id');
    }

    /**
     * Get the delivery profile associated with the user.
     */
    public function deliveryPersonnel(): HasOne
    {
        return $this->hasOne(DeliveryPersonnel::class, 'id');
    }

    /**
     * Get the products for the vendor user.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the customer orders for this user.
     */
    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get the delivery orders for this user.
     */
    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_id');
    }

    /**
     * Check if user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->role->name === 'customer';
    }

    /**
     * Check if user is a vendor.
     */
    public function isVendor(): bool
    {
        return $this->role->name === 'vendor';
    }

    /**
     * Check if user is delivery personnel.
     */
    public function isDelivery(): bool
    {
        return $this->role->name === 'delivery';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }
}