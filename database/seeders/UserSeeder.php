<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $departments = Department::all();

        foreach ($departments as $department) {
            User::create([
                'department_id' => $department->id,
                'name' => "{$department->name} Manager",
                'email' => strtolower($department->name) . '.manager@example.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]);

            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'department_id' => $department->id,
                    'name' => "{$department->name} Member {$i}",
                    'email' => strtolower($department->name) . ".member{$i}@example.com",
                    'password' => Hash::make('password'),
                    'role' => 'team_member',
                ]);
            }
        }
    }
}
