<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'created_by' => ['required', 'integer'],
            'title' => ['required', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'due_date' => ['nullable', 'date'],
            'assignee_ids' => ['required', 'array'],
            'assignee_ids.*' => ['required', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['created_by' => $this->user()->id]);
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
