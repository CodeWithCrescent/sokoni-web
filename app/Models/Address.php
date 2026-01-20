<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'label',
        'address',
        'area',
        'city',
        'latitude',
        'longitude',
        'phone',
        'instructions',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setAsDefault(): void
    {
        $this->user->addresses()->where('id', '!=', $this->id)->update(['is_default' => false]);
        $this->is_default = true;
        $this->save();
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [$this->address];
        if ($this->area) $parts[] = $this->area;
        $parts[] = $this->city;
        return implode(', ', $parts);
    }
}
