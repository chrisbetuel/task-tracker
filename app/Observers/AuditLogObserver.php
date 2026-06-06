<?php

namespace App\Observers;

use App\Models\AuditLog;

class AuditLogObserver
{
    public function creating(AuditLog $auditLog): void
    {
        if (empty($auditLog->id)) {
            $auditLog->id = (string) \Illuminate\Support\Str::uuid();
        }
    }
}
