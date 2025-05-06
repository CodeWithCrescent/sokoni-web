<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryPersonnel extends Model
{
    use HasFactory;
    
    protected $table = 'delivery_personnel';
    
    protected $fillable = ['id', 'license_plate', 'status'];

    /**
     * The "primary key" for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Get the user that owns the delivery personnel profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    /**
     * Get the orders for the delivery personnel.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_id');
    }
}