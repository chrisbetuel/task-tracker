<?php

namespace App\Enums;

enum TaskStatus: string
{
    case PendingAccept = 'pending_accept';
    case Accepted = 'accepted';
    case InProgress = 'in_progress';
    case Blocked = 'blocked';
    case Done = 'done';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PendingAccept => 'Pending Accept',
            self::Accepted => 'Accepted',
            self::InProgress => 'In Progress',
            self::Blocked => 'Blocked',
            self::Done => 'Done',
            self::Rejected => 'Rejected',
        };
    }

    public static function allowedTransitions(self $status): array
    {
        return match ($status) {
            self::PendingAccept => [self::Accepted, self::Rejected],
            self::Accepted => [self::InProgress, self::PendingAccept],
            self::InProgress => [self::Blocked, self::Done, self::PendingAccept],
            self::Blocked => [self::InProgress],
            self::Done => [self::InProgress],
            self::Rejected => [],
        };
    }
}
