<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use UuidTrait;

    protected $fillable = [
        'user_id',
        'department_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'json',
            'new_values' => 'json',
            'metadata' => 'json',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDescriptionAttribute(): string
    {
        $meta = $this->metadata ?: [];

        return match ($this->action) {
            'task_assigned' => sprintf(
                'Assigned "%s" to %s',
                $this->auditable?->title ?? 'a task',
                isset($meta['assigned_to']) ? (User::find($meta['assigned_to'])?->name ?? 'someone') : 'someone'
            ),
            'task_created' => sprintf(
                'Created task "%s"',
                $this->auditable?->title ?? 'a task'
            ),
            'task_accepted' => sprintf(
                '%s accepted task "%s"',
                isset($meta['user_id']) ? (User::find($meta['user_id'])?->name ?? 'Someone') : 'Someone',
                $this->auditable?->title ?? 'a task'
            ),
            'task_rejected' => sprintf(
                '%s rejected task "%s"%s',
                isset($meta['user_id']) ? (User::find($meta['user_id'])?->name ?? 'Someone') : 'Someone',
                $this->auditable?->title ?? 'a task',
                isset($meta['reason']) ? ": {$meta['reason']}" : ''
            ),
            'task_started' => sprintf(
                '%s started working on "%s"',
                isset($meta['user_id']) ? (User::find($meta['user_id'])?->name ?? 'Someone') : 'Someone',
                $this->auditable?->title ?? 'a task'
            ),
            'task_blocked' => sprintf(
                '%s reported blockage on "%s"%s',
                isset($meta['user_id']) ? (User::find($meta['user_id'])?->name ?? 'Someone') : 'Someone',
                $this->auditable?->title ?? 'a task',
                isset($meta['reason']) ? ": {$meta['reason']}" : ''
            ),
            'task_unblocked' => sprintf(
                '%s resolved blockage on "%s"',
                isset($meta['user_id']) ? (User::find($meta['user_id'])?->name ?? 'Someone') : 'Someone',
                $this->auditable?->title ?? 'a task'
            ),
            'task_completed' => sprintf(
                '%s marked "%s" as done',
                isset($meta['user_id']) ? (User::find($meta['user_id'])?->name ?? 'Someone') : 'Someone',
                $this->auditable?->title ?? 'a task'
            ),
            'task_reopened' => sprintf(
                '%s reopened "%s"',
                isset($meta['user_id']) ? (User::find($meta['user_id'])?->name ?? 'Someone') : 'Someone',
                $this->auditable?->title ?? 'a task'
            ),
            'task_unassigned' => sprintf(
                '%s unassigned from "%s"',
                isset($meta['user_id']) ? (User::find($meta['user_id'])?->name ?? 'Someone') : 'Someone',
                $this->auditable?->title ?? 'a task'
            ),
            default => str_replace('_', ' ', ucfirst($this->action)) . ' ' . ($this->auditable?->title ?? '')
        };
    }

    public function scopeForDepartment(Builder $query, string $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeOfAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }
}
