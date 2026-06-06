<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::Critical => 'Critical',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Low => 'bg-success',
            self::Medium => 'bg-info',
            self::High => 'bg-warning text-dark',
            self::Critical => 'bg-danger',
        };
    }
}
