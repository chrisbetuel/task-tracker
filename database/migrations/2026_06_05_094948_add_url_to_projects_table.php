<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // DB-safe: add only if missing.
        if (!Schema::hasColumn('projects', 'url')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('url')->nullable();
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
};

