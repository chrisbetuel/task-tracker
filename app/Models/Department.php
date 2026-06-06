<?php

namespace App\Models;

use App\Enums\DepartmentType;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'name',
        'description',
        'type',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'type' => DepartmentType::class,
            'settings' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function manager(): ?User
    {
        return $this->users()->where('role', 'manager')->first();
    }

    public function teamMembers()
    {
        return $this->users()->where('role', 'team_member');
    }

    public function isMarketing(): bool
    {
        return $this->type === DepartmentType::Marketing;
    }

    public function isAgent(): bool
    {
        return $this->type === DepartmentType::Agent;
    }

    public function isGeneral(): bool
    {
        return $this->type === DepartmentType::General;
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
