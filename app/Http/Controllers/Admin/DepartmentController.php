<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DepartmentType;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount(['users', 'projects', 'tasks'])->latest()->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $types = DepartmentType::cases();
        return view('admin.departments.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'type' => ['required', 'string', 'in:general,marketing,agent'],
            'settings' => ['nullable', 'json'],
        ]);

        $validated['settings'] = $validated['settings'] ?? $this->defaultSettings($validated['type']);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['users', 'projects', 'tasks' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $types = DepartmentType::cases();
        return view('admin.departments.edit', compact('department', 'types'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'type' => ['required', 'string', 'in:general,marketing,agent'],
            'settings' => ['nullable', 'json'],
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    private function defaultSettings(string $type): ?array
    {
        return match ($type) {
            'marketing' => [
                'channels' => ['email', 'social', 'paid_ads', 'seo'],
                'default_calendar_view' => 'month',
                'approval_required' => true,
            ],
            'agent' => [
                'sla_response_hours' => 4,
                'sla_resolution_hours' => 24,
                'auto_assign' => false,
            ],
            default => null,
        };
    }
}
