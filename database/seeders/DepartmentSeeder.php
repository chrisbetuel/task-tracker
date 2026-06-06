<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::create([
            'name' => 'Engineering',
            'description' => 'Software engineering and product development team.',
            'type' => 'general',
        ]);

        Department::create([
            'name' => 'Marketing',
            'description' => 'Marketing campaigns, content creation, and brand management.',
            'type' => 'marketing',
            'settings' => [
                'channels' => ['email', 'social', 'paid_ads', 'seo'],
                'default_calendar_view' => 'month',
                'approval_required' => true,
            ],
        ]);

        Department::create([
            'name' => 'Sales',
            'description' => 'Sales and business development department.',
            'type' => 'general',
        ]);

        Department::create([
            'name' => 'Customer Support',
            'description' => 'Customer support and help desk team.',
            'type' => 'agent',
            'settings' => [
                'sla_response_hours' => 2,
                'sla_resolution_hours' => 24,
                'auto_assign' => false,
            ],
        ]);

        Department::create([
            'name' => 'Human Resources',
            'description' => 'HR and talent management department.',
            'type' => 'general',
        ]);
    }
}
