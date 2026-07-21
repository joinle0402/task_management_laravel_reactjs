<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\TaskChecklistItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
        $task = $this->route('model');
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
            'priority' => ['sometimes', Rule::enum(TaskPriority::class)],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'assignee_ids' => ['sometimes', 'array'],
            'assignee_ids.*' => ['required', 'integer'],
            'checklist_items' => 'sometimes|array',
            'checklist_items.*.id' => 'sometimes|integer',
            'checklist_items.*.name' => 'required|string|max:255',
            'checklist_items.*.done' => 'sometimes|boolean',
        ];
    }

    protected function after(): array
    {
        return [
            function (Validator $validator) {
                $this->validateIdsExist($validator, 'assignee_ids', User::query());
                $this->validateIdsExist($validator, 'checklist_items', TaskChecklistItem::whereTaskId($this->route('model')->id));
            },
        ];
    }

    private function validateIdsExist(Validator $validator, string $fieldname, Builder $query): void
    {
        $request = collect($this->input($fieldname, []));
        $requestIds = $request->pluck('id')->filter()->unique()->values();
        if (empty($requestIds)) return;
        $existingIds = $query->whereIn('id', $requestIds)->pluck('id')->all();
        $missingIds  = $requestIds->diff($existingIds);
        if ($missingIds->isNotEmpty()) {
            $validator->errors()->add($fieldname,'Các ID không hợp lệ: '.$missingIds->implode(', '));
        }
    }
}
