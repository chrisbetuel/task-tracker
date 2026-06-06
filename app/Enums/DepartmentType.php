<?php

namespace App\Enums;

enum DepartmentType: string
{
    case General = 'general';
    case Marketing = 'marketing';
    case Agent = 'agent';

    public function label(): string
    {
        return match ($this) {
            self::General => 'General',
            self::Marketing => 'Marketing',
            self::Agent => 'Agent (Support)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::General => 'bi-building',
            self::Marketing => 'bi-megaphone',
            self::Agent => 'bi-headset',
        };
    }
}
