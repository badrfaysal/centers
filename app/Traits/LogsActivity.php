<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity("created", null, $model->getAttributes());
        });

        static::updated(function ($model) {
            if ($model->isDirty()) {
                $model->logActivity("updated", $model->getOriginal(), $model->getAttributes());
            }
        });

        static::deleted(function ($model) {
            $model->logActivity("deleted", $model->getAttributes(), null);
        });
    }

    protected function logActivity($action, $oldValues = null, $newValues = null)
    {
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        AuditLog::create([
            "user_id" => Auth::id(),
            "action" => $action,
            "model_type" => get_class($this),
            "model_id" => $this->id,
            "old_values" => $oldValues,
            "new_values" => $newValues,
            "ip_address" => request()->ip(),
            "user_agent" => request()->userAgent(),
        ]);
    }
}
