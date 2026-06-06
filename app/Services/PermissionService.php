<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\User;

class PermissionService
{
    public function userCanViewDepartment(User $user, Department $department): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->department_id === $department->id;
    }

    public function userCanManageDepartment(User $user, Department $department): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isManager() && $user->department_id === $department->id;
    }

    public function userCanManageUser(User $user, User $targetUser): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isManager() && $user->department_id === $targetUser->department_id) {
            return true;
        }

        return $user->id === $targetUser->id;
    }

    public function userCanViewAuditLogs(User $user, ?Department $department = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isManager() && $department && $user->department_id === $department->id) {
            return true;
        }

        return false;
    }
}
