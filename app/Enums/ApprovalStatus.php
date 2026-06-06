<?php

namespace App\Enums;

enum ApprovalStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Approved = 'approved';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Review => 'In Review',
            self::Approved => 'Approved',
            self::Published => 'Published',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-secondary',
            self::Review => 'bg-info',
            self::Approved => 'bg-success',
            self::Published => 'bg-primary',
        };
    }
}
