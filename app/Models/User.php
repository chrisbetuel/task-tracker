<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, UuidTrait;

    protected $fillable = [
        'department_id',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'user_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    public function blockages(): HasMany
    {
        return $this->hasMany(Blockage::class, 'reported_by');
    }

    public function resolvedBlockages(): HasMany
    {
        return $this->hasMany(Blockage::class, 'resolved_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function createdCampaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'created_by');
    }

    public function createdClients(): HasMany
    {
        return $this->hasMany(Client::class, 'created_by');
    }

    public function createdAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'created_by');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(TaskRating::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isHeadOfOperation(): bool
    {
        return $this->role === UserRole::HeadOfOperation;
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function isTeamMember(): bool
    {
        return $this->role === UserRole::TeamMember;
    }
}
