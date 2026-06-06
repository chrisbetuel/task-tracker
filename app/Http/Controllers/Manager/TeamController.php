<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;

class TeamController extends Controller
{
    public function index()
    {
        $department = request()->user()->department;

        $teamMembers = User::where('department_id', $department->id)
            ->where('role', 'team_member')
            ->withCount(['assignedTasks', 'timeLogs'])
            ->get();

        return view('manager.team.index', compact('teamMembers'));
    }
}
