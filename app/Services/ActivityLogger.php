<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log a system activity.
     *
     * @param  string  $action  'created', 'updated', or 'deleted'
     * @param  mixed  $model  The model being acted upon
     * @param  string  $description  Human-readable description
     * @param  array|null  $metadata  Additional data (like diffs)
     */
    public static function log(string $action, $model, string $description, ?array $metadata = null): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'System',
            'action' => $action,
            'model_type' => class_basename($model),
            'model_id' => $model->id ?? null,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
