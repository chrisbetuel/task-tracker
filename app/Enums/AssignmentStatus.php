<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case Assigned = 'assigned';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Unassigned = 'unassigned';

    public function label(): string
    {
        return match ($this) {
            self::Assigned => 'Assigned',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
            self::Unassigned => 'Unassigned',
        };
    }
}
