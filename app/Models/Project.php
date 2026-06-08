<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'department_id',
        'parent_project_id',
        'campaign_id',
        'name',
        'description',
        'url',
        'status',
        'created_by',
    ];

    public function effectiveUrl(): ?string
    {
        if ($this->url) {
            return $this->url;
        }
        if ($this->parent) {
            return $this->parent->effectiveUrl();
        }
        return null;
    }

    public function computedStatus(): string
    {
        if ($this->status) {
            return $this->status;
        }
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 'pending';
        }
        $done = $this->tasks()->where('status', TaskStatus::Done)->count();
        if ($done === $total) {
            return 'accomplished';
        }
        return 'in_progress';
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'parent_project_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Project::class, 'parent_project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function allDescendantIds(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->allDescendantIds());
        }
        return $ids;
    }
}
