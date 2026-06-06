<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'due_date')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->date('due_date')->nullable();
            });
        }

        if (!Schema::hasColumn('tasks', 'estimated_minutes')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->integer('estimated_minutes')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tasks', 'due_date')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('due_date');
            });
        }

        if (Schema::hasColumn('tasks', 'estimated_minutes')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('estimated_minutes');
            });
        }
    }
};

