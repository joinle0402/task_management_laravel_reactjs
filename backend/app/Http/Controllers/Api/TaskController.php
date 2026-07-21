<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\SearchTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class TaskController extends Controller
{
    public function index(SearchTaskRequest $request): AnonymousResourceCollection
    {
        return TaskResource::collection(Task::query()
                ->with('createdBy', 'assignees', 'checklistItems')
                ->when($request->validated('keyword'), fn ($query, $keyword) => $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%$keyword%")->orWhere('description', 'like', "%$keyword%");
                }))
                ->when($request->validated('status'), fn ($query, $status) => $query->where('status', $status))
                ->when($request->validated('priority'), fn ($query, $priority) => $query->where('priority', $priority))
                ->when($request->validated('created_by'), fn ($query, $created_by) => $query->where('created_by', $created_by))
                ->when($request->validated('due_date_from'), fn ($query, $due_date_from) => $query->where('due_date', '>=', $due_date_from))
                ->when($request->validated('due_date_to'), fn ($query, $due_date_to) => $query->where('due_date', '<=', $due_date_to))
                ->latest()
                ->paginate($request->validated('limit', 100))
                ->withQueryString()
        );
    }

    /**
     * @throws Throwable
     */
    public function store(StoreTaskRequest $request): TaskResource
    {
        $validated = $request->validated();
        $model = DB::transaction(function () use ($validated) {
            $task = Task::create(collect($validated)->except('assignee_ids', 'checklist_items')->all());
            $task->assignees()->attach($validated['assignee_ids'] ?? []);

            foreach ($validated['checklist_items'] ?? [] as $position => $item) {
                $task->checklistItems()->create([
                    'name' => trim($item['name']),
                    'done' => $item['done'] ?? false,
                    'position' => $position,
                ]);
            }

            return $task->load('assignees', 'checklistItems');
        });
        return new TaskResource($model);
    }

    public function show(Task $model): TaskResource
    {
        return new TaskResource($model->load('assignees', 'checklistItems'));
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateTaskRequest $request, Task $model): TaskResource
    {
        $validated = $request->validated();
        $model = DB::transaction(function () use ($validated, $model)  {
            $model->update(collect($validated)->except('assignee_ids', 'checklist_items')->all());

            if (array_key_exists('assignee_ids', $validated)) {
                $model->assignees()->sync($validated['assignee_ids']);
            }

            if (array_key_exists('checklist_items', $validated)) {
                $this->syncChecklistItems($model, $validated['checklist_items']);
            }

            return $model->load('createdBy', 'assignees', 'checklistItems');
        });
        return new TaskResource($model);
    }



    public function destroy(Task $model): Response
    {
        $model->delete();
        return response()->noContent();
    }

    private function syncChecklistItems(Task $task, array $checklistItems)
    {
        $existingItems = $task->checklistItems()->pluck('id')->toArray();
        $requestItems = collect($checklistItems)->pluck('id')->filter()->map(fn ($id) => (int) $id)->values()->toArray();

        $itemIdsToDelete = array_diff($existingItems, $requestItems);
        if (!empty($itemIdsToDelete)) {
            $task->checklistItems()->whereIn('id', $itemIdsToDelete)->delete();
        }

        foreach ($checklistItems as $position => $item) {
            $checklist = [];
            $checklist['name'] = $item['name'];
            $checklist['done'] = $item['done'] ?? false;
            $checklist['position'] = $position + 1;
            if (!empty($item['id'])) {
                $task->checklistItems()->whereKey($item['id'])->update($checklist);
            } else {
                $task->checklistItems()->create($checklist);
            }
        }
    }
}
