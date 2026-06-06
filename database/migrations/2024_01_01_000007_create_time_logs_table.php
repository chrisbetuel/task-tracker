<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->integer('minutes');
            $table->date('logged_date');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('task_id');
            $table->index('user_id');
            $table->index('logged_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
