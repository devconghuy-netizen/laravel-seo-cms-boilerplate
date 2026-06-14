<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditLogService
{
    public function log(
        Request $request,
        Model|string $model,
        string $action,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $request->user()?->id,
            'model_type' => is_string($model) ? $model : $model::class,
            'model_id' => is_string($model) ? 0 : $model->getKey(),
            'action' => $action,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'correlation_id' => (string) Str::uuid(),
            'description' => $description,
        ]);
    }
}
