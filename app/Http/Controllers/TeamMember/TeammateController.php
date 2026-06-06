<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamMember\StoreTeamMemberRequest;
use App\Models\User;
use App\Services\ReportingService;
use Illuminate\Support\Facades\Hash;

class TeammateController extends Controller
{
    public function index()
    {
        $user = request()->user();

        $teammates = User::where('department_id', $user->department_id)
            ->where('role', 'team_member')
            ->where('id', '!=', $user->id)
            ->withCount(['assignedTasks', 'timeLogs'])
            ->get();

        return view('member.teammates.index', compact('teammates'));
    }

    public function create()
    {
        return view('member.teammates.create');
    }

    public function store(StoreTeamMemberRequest $request)
    {
        $user = request()->user();

        User::create([
            'department_id' => $user->department_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'team_member',
        ]);

        return redirect()->route('member.teammates.index')
            ->with('success', 'Team member added successfully.');
    }

    public function show(User $teammate, ReportingService $reporting)
    {
        $user = request()->user();

        if ($teammate->department_id !== $user->department_id) {
            abort(403);
        }

        $profile = $reporting->teammateProfile($teammate, $user);

        return view('member.teammates.show', compact('profile'));
    }
}
