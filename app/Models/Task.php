<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\MarketingChannel;
use App\Enums\TaskStatus;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'project_id',
        'department_id',
        'title',
        'description',
        'status',
        'assigned_to',
        'created_by',
        'parent_task_id',
        'client_id',
        'campaign_id',
        'channel',
        'approval_status',
        'priority',
        'due_date',
        'estimated_minutes',
        'sla_response_minutes',
        'sla_resolution_minutes',
        'first_responded_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => \App\Enums\TaskPriority::class,
            'approval_status' => ApprovalStatus::class,
            'channel' => MarketingChannel::class,
            'due_date' => 'date',
            'first_responded_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast()
            && !in_array($this->status->value, [TaskStatus::Done->value, TaskStatus::Rejected->value]);
    }

    public function isUrgent(): bool
    {
        return $this->due_date && $this->due_date->isToday()
            && !in_array($this->status->value, [TaskStatus::Done->value, TaskStatus::Rejected->value]);
    }

    public function isSlaBreached(): bool
    {
        if (!$this->sla_response_minutes || !$this->created_at) {
            return false;
        }
        if ($this->first_responded_at) {
            return false;
        }
        return $this->created_at->diffInMinutes(now()) > $this->sla_response_minutes;
    }

    public function slaDeadline(): ?\Carbon\Carbon
    {
        if (!$this->sla_response_minutes || !$this->created_at) {
            return null;
        }
        return $this->created_at->copy()->addMinutes($this->sla_response_minutes);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function effectiveProjectUrl(): ?string
    {
        return $this->project?->effectiveUrl();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(TaskAssignment::class)->latestOfMany();
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    public function blockages(): HasMany
    {
        return $this->hasMany(Blockage::class);
    }

    public function activeBlockages()
    {
        return $this->blockages()->whereNull('resolved_at');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(TaskRating::class);
    }

    public function totalTimeLogged(): int
    {
        return (int) $this->timeLogs()->sum('minutes');
    }

    public function hasTimeLogged(): bool
    {
        return $this->timeLogs()->exists();
    }
}
