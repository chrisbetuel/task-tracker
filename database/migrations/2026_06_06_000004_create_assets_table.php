<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('department_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('task_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('document');
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
            $table->index('department_id');
            $table->index('task_id');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
