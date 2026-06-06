<?php

return [
    'roles' => [
        'admin' => 'admin',
        'manager' => 'manager',
        'team_member' => 'team_member',
    ],

    'permissions' => [
        'admin' => [
            'view_all_departments',
            'manage_departments',
            'manage_all_users',
            'view_all_audit_logs',
            'view_all_reports',
        ],
        'manager' => [
            'view_department',
            'manage_projects',
            'create_tasks',
            'assign_tasks',
            'view_department_audit_logs',
            'view_department_reports',
            'manage_team',
        ],
        'team_member' => [
            'view_assigned_tasks',
            'claim_unassigned_tasks',
            'create_projects',
            'log_time',
            'report_blockages',
            'reopen_tasks',
            'view_teammate_profiles',
        ],
    ],
];
