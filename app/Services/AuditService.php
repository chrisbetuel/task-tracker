<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public function log(string $action, Model $auditable, array $metadata = []): AuditLog
    {
        $user = request()?->user();

        $departmentId = match (true) {
            $auditable instanceof \App\Models\Task => $auditable->department_id,
            $auditable instanceof \App\Models\Project => $auditable->department_id,
            $auditable instanceof \App\Models\User => $auditable->department_id,
            default => $user?->department_id,
        };

        return AuditLog::create([
            'user_id' => $user?->id,
            'department_id' => $departmentId,
            'action' => $action,
            'auditable_type' => get_class($auditable),
            'auditable_id' => $auditable->getKey(),
            'metadata' => $metadata,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    public function logWithValues(string $action, Model $auditable, ?array $oldValues = null, ?array $newValues = null, array $metadata = []): AuditLog
    {
        $log = $this->log($action, $auditable, $metadata);
        $log->update([
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
        return $log->fresh();
    }
}
