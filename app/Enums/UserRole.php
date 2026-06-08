<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case HeadOfOperation = 'head_of_operation';
    case Manager = 'manager';
    case TeamMember = 'team_member';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::HeadOfOperation => 'Head of Operation',
            self::Manager => 'Manager',
            self::TeamMember => 'Team Member',
        };
    }
}
