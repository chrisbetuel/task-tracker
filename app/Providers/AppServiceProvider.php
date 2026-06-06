<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Task;
use App\Observers\AuditLogObserver;
use App\Observers\TaskObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        AuditLog::observe(AuditLogObserver::class);
    }
}
