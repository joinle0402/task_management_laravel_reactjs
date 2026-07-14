<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::enum(TaskStatus::class)],
            'priority' => ['nullable', Rule::enum(TaskPriority::class)],
            'created_by' => 'nullable|integer',
            'due_date_from' => 'nullable|date',
            'due_date_to' => 'nullable|date|after_or_equal:due_date_from',
            'limit' => 'nullable|integer|min:1',
        ];
    }
}
