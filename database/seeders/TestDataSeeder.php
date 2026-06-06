<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedGeneralDepartments();
        $this->seedMarketingData();
        $this->seedAgentData();
    }

    private function seedGeneralDepartments(): void
    {
        $engineering = Department::where('type', 'general')->first();
        if (!$engineering) return;

        $manager = User::where('department_id', $engineering->id)->where('role', 'manager')->first();
        if (!$manager) return;

        $members = User::where('department_id', $engineering->id)->where('role', 'team_member')->get();

        $project = Project::create([
            'department_id' => $engineering->id,
            'name' => 'Core Platform v2',
            'description' => 'Main product development initiative.',
            'created_by' => $manager->id,
        ]);

        foreach ($members as $member) {
            Task::create([
                'project_id' => $project->id,
                'department_id' => $engineering->id,
                'title' => 'Implement user authentication',
                'status' => TaskStatus::Done,
                'assigned_to' => $member->id,
                'created_by' => $manager->id,
                'priority' => TaskPriority::High,
                'due_date' => now()->subDays(2),
            ]);

            Task::create([
                'project_id' => $project->id,
                'department_id' => $engineering->id,
                'title' => 'Design dashboard UI',
                'status' => TaskStatus::InProgress,
                'assigned_to' => $member->id,
                'created_by' => $manager->id,
                'priority' => TaskPriority::Medium,
                'due_date' => now()->addDays(5),
            ]);
        }
    }

    private function seedMarketingData(): void
    {
        $marketing = Department::where('type', 'marketing')->first();
        if (!$marketing) return;

        $manager = User::where('department_id', $marketing->id)->where('role', 'manager')->first();
        if (!$manager) return;

        $members = User::where('department_id', $marketing->id)->where('role', 'team_member')->get();
        if ($members->isEmpty()) return;

        // Create campaigns
        $campaigns = [];
        $campaignData = [
            ['name' => 'Summer Sale 2026', 'start' => now()->subDays(10), 'end' => now()->addDays(20)],
            ['name' => 'Product Launch Q3', 'start' => now()->addDays(5), 'end' => now()->addMonths(2)],
            ['name' => 'Brand Awareness', 'start' => now()->subMonth(), 'end' => now()->addMonths(3)],
        ];

        foreach ($campaignData as $data) {
            $campaigns[] = Campaign::create([
                'department_id' => $marketing->id,
                'name' => $data['name'],
                'start_date' => $data['start'],
                'end_date' => $data['end'],
                'status' => 'active',
                'created_by' => $manager->id,
            ]);
        }

        // Create projects under campaigns
        $project1 = Project::create([
            'department_id' => $marketing->id,
            'name' => 'Email Campaign',
            'description' => 'Email marketing for Summer Sale',
            'created_by' => $manager->id,
        ]);

        $project2 = Project::create([
            'department_id' => $marketing->id,
            'name' => 'Social Media Content',
            'description' => 'Social media posts and ads',
            'created_by' => $manager->id,
        ]);

        // Create marketing tasks with approval workflow
        $contentTasks = [
            ['title' => 'Write summer sale email copy', 'channel' => 'email', 'approval' => ApprovalStatus::Draft, 'campaign' => 0],
            ['title' => 'Design email banner', 'channel' => 'email', 'approval' => ApprovalStatus::Review, 'campaign' => 0],
            ['title' => 'Create Facebook ad creative', 'channel' => 'social', 'approval' => ApprovalStatus::Approved, 'campaign' => 0],
            ['title' => 'Write blog post - product features', 'channel' => 'seo', 'approval' => ApprovalStatus::Draft, 'campaign' => 1],
            ['title' => 'Instagram story series', 'channel' => 'social', 'approval' => ApprovalStatus::Review, 'campaign' => 2],
            ['title' => 'Google Ads copy', 'channel' => 'paid_ads', 'approval' => ApprovalStatus::Draft, 'campaign' => 0],
        ];

        foreach ($contentTasks as $i => $ct) {
            Task::create([
                'project_id' => $i < 3 ? $project1->id : $project2->id,
                'department_id' => $marketing->id,
                'title' => $ct['title'],
                'status' => TaskStatus::InProgress,
                'assigned_to' => $members[$i % $members->count()]->id,
                'created_by' => $manager->id,
                'priority' => TaskPriority::High,
                'channel' => $ct['channel'],
                'approval_status' => $ct['approval'],
                'campaign_id' => $campaigns[$ct['campaign']]->id,
                'due_date' => $i < 2 ? now()->addDays($i * 2) : now()->addDays(5 + $i),
            ]);
        }

        Task::create([
            'project_id' => $project1->id,
            'department_id' => $marketing->id,
            'title' => 'Review and approve email banner',
            'status' => TaskStatus::PendingAccept,
            'created_by' => $manager->id,
            'priority' => TaskPriority::Medium,
            'approval_status' => ApprovalStatus::Review,
            'due_date' => now()->addDay(),
        ]);
    }

    private function seedAgentData(): void
    {
        $support = Department::where('type', 'agent')->first();
        if (!$support) return;

        $manager = User::where('department_id', $support->id)->where('role', 'manager')->first();
        if (!$manager) return;

        $members = User::where('department_id', $support->id)->where('role', 'team_member')->get();
        if ($members->isEmpty()) return;

        // Create clients
        $clients = [];
        $clientNames = ['Acme Corp', 'Globex Inc', 'Initech', 'Umbrella Co', 'Stark Industries'];
        foreach ($clientNames as $name) {
            $clients[] = Client::create([
                'department_id' => $support->id,
                'name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)) . '@example.com',
                'company' => $name,
                'created_by' => $manager->id,
            ]);
        }

        $project = Project::create([
            'department_id' => $support->id,
            'name' => 'Support Tickets',
            'description' => 'Customer support ticket system',
            'created_by' => $manager->id,
        ]);

        // Create ticket tasks
        $tickets = [
            ['title' => 'Cannot login to account', 'priority' => TaskPriority::Critical, 'client' => 0, 'sla' => 60, 'status' => TaskStatus::InProgress, 'member' => 0],
            ['title' => 'Billing inquiry for invoice #4421', 'priority' => TaskPriority::High, 'client' => 1, 'sla' => 120, 'status' => TaskStatus::InProgress, 'member' => 1],
            ['title' => 'Feature request: dark mode', 'priority' => TaskPriority::Low, 'client' => 2, 'sla' => null, 'status' => TaskStatus::PendingAccept, 'member' => null],
            ['title' => 'Account upgrade not processing', 'priority' => TaskPriority::Critical, 'client' => 3, 'sla' => 30, 'status' => TaskStatus::PendingAccept, 'member' => null],
            ['title' => 'Password reset not working', 'priority' => TaskPriority::Medium, 'client' => 4, 'sla' => null, 'status' => TaskStatus::Done, 'member' => 0],
            ['title' => 'Integration setup help', 'priority' => TaskPriority::Medium, 'client' => 0, 'sla' => 240, 'status' => TaskStatus::PendingAccept, 'member' => null],
        ];

        $hoursAgo = 0;
        foreach ($tickets as $tix) {
            $created = now()->subHours($hoursAgo + 1);
            $task = Task::create([
                'project_id' => $project->id,
                'department_id' => $support->id,
                'title' => $tix['title'],
                'status' => $tix['status'],
                'assigned_to' => $tix['member'] !== null ? $members[$tix['member']]->id : null,
                'created_by' => $manager->id,
                'priority' => $tix['priority'],
                'client_id' => $clients[$tix['client']]->id,
                'sla_response_minutes' => $tix['sla'],
                'sla_resolution_minutes' => $tix['sla'] ? $tix['sla'] * 4 : null,
                'first_responded_at' => $tix['member'] !== null ? $created->copy()->addMinutes(15) : null,
                'created_at' => $created,
                'updated_at' => $created,
            ]);

            if ($tix['member'] !== null && $hoursAgo === 0) {
                $task->timestamps = false;
                $task->updateQuietly();
            }

            $hoursAgo += 2;
        }

        // Create a dangerously overdue SLA ticket
        Task::create([
            'project_id' => $project->id,
            'department_id' => $support->id,
            'title' => 'Production system down - urgent',
            'status' => TaskStatus::InProgress,
            'assigned_to' => $members[0]->id,
            'created_by' => $manager->id,
            'priority' => TaskPriority::Critical,
            'client_id' => $clients[1]->id,
            'sla_response_minutes' => 15,
            'sla_resolution_minutes' => 120,
            'created_at' => now()->subHours(3),
            'updated_at' => now()->subHours(3),
        ]);
    }
}
