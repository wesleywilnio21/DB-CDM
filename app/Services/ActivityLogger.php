<?php

namespace App\Services;

class ActivityLogger
{
    /**
     * Log a system activity.
     *
     * @param string $action 'created', 'updated', or 'deleted'
     * @param mixed $model The model being acted upon
     * @param string $description Human-readable description
     * @param array|null $metadata Additional data (like diffs)
     */
    public static function log(string $action, $model, string $description, ?array $metadata = null): void
    {
        $activity = activity()
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->event($action);
            
        if ($metadata) {
            $activity->withProperties($metadata);
        }
            
        $activity->log($description);
    }
}
