<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $model->logActivity('updated', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', $model->getAttributes(), null);
        });
    }

    protected function logActivity($event, $oldValues = null, $newValues = null)
    {
        if (! Auth::check()) {
            return;
        }

        $properties = [
            'old' => $oldValues,
            'new' => $newValues,
        ];

        // Filter out hidden attributes if necessary, or sensitive data
        // For now, we log everything as this is an explicit requirement.

        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'subject_type' => get_class($this),
                'subject_id' => $this->id,
                'event' => $event,
                'description' => $this->getActivityDescription($event),
                'properties' => $properties,
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to log activity: " . $e->getMessage());
        }
    }

    protected function getActivityDescription($event)
    {
        $className = class_basename($this);
        $eventName = ucfirst($event);

        return "{$eventName} {$className}";
    }
}
