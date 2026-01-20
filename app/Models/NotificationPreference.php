<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'email_order_updates',
        'email_promotions',
        'sms_order_updates',
        'sms_promotions',
        'push_order_updates',
        'push_promotions',
    ];

    protected function casts(): array
    {
        return [
            'email_order_updates' => 'boolean',
            'email_promotions' => 'boolean',
            'sms_order_updates' => 'boolean',
            'sms_promotions' => 'boolean',
            'push_order_updates' => 'boolean',
            'push_promotions' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
