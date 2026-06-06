<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Permanent, DB-safe fix:
        // - If this environment already has the column (e.g., because the older migration ran), do nothing.
        // - If the column is missing, add it.
        //
        // Note: For SQLite (used in tests), `after()` is not supported; keep the migration compatible.
        if (!Schema::hasColumn('projects', 'url')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('url')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Only remove if it exists.
        if (Schema::hasColumn('projects', 'url')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('url');
            });
        }
    }
};


