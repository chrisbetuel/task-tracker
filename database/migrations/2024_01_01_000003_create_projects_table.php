<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('department_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('parent_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();

            $table->index('department_id');
            $table->index('parent_project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
