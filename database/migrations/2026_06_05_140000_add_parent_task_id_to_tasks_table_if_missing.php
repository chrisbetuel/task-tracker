<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'parent_task_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignUuid('parent_task_id')
                    ->nullable()
                    ->constrained('tasks')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tasks', 'parent_task_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['parent_task_id']);
                $table->dropColumn('parent_task_id');
            });
        }
    }
};

