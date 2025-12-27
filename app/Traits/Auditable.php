<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            self::logAudit($model, 'created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();
            
            unset($changes['updated_at']);
            
            if (!empty($changes)) {
                $oldValues = array_intersect_key($original, $changes);
                self::logAudit($model, 'updated', $oldValues, $changes);
            }
        });

        static::deleted(function ($model) {
            $event = $model->isForceDeleting() ? 'force_deleted' : 'deleted';
            self::logAudit($model, $event, $model->getAttributes(), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                self::logAudit($model, 'restored', null, $model->getAttributes());
            });
        }
    }

    protected static function logAudit($model, string $event, ?array $oldValues, ?array $newValues): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
        ]);
    }
}
