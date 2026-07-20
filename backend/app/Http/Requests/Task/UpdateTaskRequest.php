<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
            'priority' => ['sometimes', Rule::enum(TaskPriority::class)],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'assignee_ids' => ['sometimes', 'array'],
            'assignee_ids.*' => ['required', 'integer'],
        ];
    }

    protected function after(): array
    {
        return [
            function (Validator $validator) {
                $userIds = collect($this->input('assignee_ids', []))->filter(fn ($id) => is_int($id) || ctype_digit($id))->unique()->values();
                if (!$userIds->isEmpty()) {
                    $existingUserIds = User::whereIn('id', $userIds)->pluck('id');
                    $missingUserIds = $userIds->diff($existingUserIds);
                    if (!$missingUserIds->isEmpty()) {
                        $validator->errors()->add('assignee_ids', 'Các người dùng không tồn tại: '.$missingUserIds->implode(', '));
                    }
                }
            },
        ];
    }
}
