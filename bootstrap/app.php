<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminOrHeadOfOperationMiddleware;
use App\Http\Middleware\HeadOfOperationMiddleware;
use App\Http\Middleware\ManagerMiddleware;
use App\Http\Middleware\TeamMemberMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'admin_or_hoo' => AdminOrHeadOfOperationMiddleware::class,
            'head_of_operation' => HeadOfOperationMiddleware::class,
            'manager' => ManagerMiddleware::class,
            'team_member' => TeamMemberMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
