<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'string', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'assigned_to' => ['nullable', 'string', 'exists:users,id'],
            'parent_task_id' => ['nullable', 'string', 'exists:tasks,id'],
            'priority' => ['required', 'string', 'in:low,medium,high,critical'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:525600'],
        ];
    }

    public function messages(): array
    {
        return [
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'estimated_minutes.max' => 'Estimated time cannot exceed one year (525,600 minutes).',
        ];
    }
}
