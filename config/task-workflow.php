<?php

return [
    'statuses' => [
        'pending_accept',
        'accepted',
        'in_progress',
        'blocked',
        'done',
        'rejected',
    ],

    'transitions' => [
        'pending_accept' => ['accepted', 'rejected'],
        'accepted' => ['in_progress', 'pending_accept'],
        'in_progress' => ['blocked', 'done', 'pending_accept'],
        'blocked' => ['in_progress'],
        'done' => ['in_progress'],
    ],

    'requires_time_logged' => [
        'done',
    ],
];
