<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'client_id')) {
                $table->foreignUuid('client_id')->nullable()->after('parent_task_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('tasks', 'campaign_id')) {
                $table->foreignUuid('campaign_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('tasks', 'channel')) {
                $table->string('channel')->nullable()->after('campaign_id');
            }
            if (!Schema::hasColumn('tasks', 'approval_status')) {
                $table->string('approval_status')->nullable()->after('channel');
            }
            if (!Schema::hasColumn('tasks', 'sla_response_minutes')) {
                $table->integer('sla_response_minutes')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('tasks', 'sla_resolution_minutes')) {
                $table->integer('sla_resolution_minutes')->nullable()->after('sla_response_minutes');
            }
            if (!Schema::hasColumn('tasks', 'first_responded_at')) {
                $table->timestamp('first_responded_at')->nullable()->after('sla_resolution_minutes');
            }
            if (!Schema::hasColumn('tasks', 'metadata')) {
                $table->json('metadata')->nullable()->after('first_responded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'client_id',
                'campaign_id',
                'channel',
                'approval_status',
                'sla_response_minutes',
                'sla_resolution_minutes',
                'first_responded_at',
                'metadata',
            ]);
        });
    }
};
