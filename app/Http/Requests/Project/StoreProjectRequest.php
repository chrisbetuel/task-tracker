<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'url' => ['nullable', 'string', 'url', 'max:2048'],
            'status' => ['nullable', 'string', 'in:pending,in_progress,accomplished'],
            'parent_project_id' => ['nullable', 'string', 'exists:projects,id'],
            'sub_projects' => ['nullable', 'array', 'max:20'],
            'sub_projects.*.name' => ['required', 'string', 'max:255'],
            'sub_projects.*.description' => ['nullable', 'string', 'max:65535'],
            'sub_projects.*.url' => ['nullable', 'string', 'url', 'max:2048'],
            'initial_tasks' => ['nullable', 'array', 'max:20'],
            'initial_tasks.*.title' => ['required', 'string', 'max:255'],
            'initial_tasks.*.assigned_to' => ['nullable', 'string', 'exists:users,id'],
            'initial_tasks.*.priority' => ['required', 'string', 'in:low,medium,high,critical'],
            'initial_tasks.*.due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'initial_tasks.*.estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:525600'],
        ];
    }

    public function messages(): array
    {
        return [
            'sub_projects.max' => 'You can add up to 20 sub-projects at once.',
            'sub_projects.*.name.required' => 'Each sub-project must have a name.',
        ];
    }
}
