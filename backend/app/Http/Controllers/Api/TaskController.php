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
                ->with('createdBy')
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
            $task = Task::create(collect($validated)->except('assignee_ids')->all());
            $task->assignees()->attach($validated['assignee_ids'] ?? []);
            return $task->load('assignees');
        });
        return new TaskResource($model);
    }

    public function show(Task $model): TaskResource
    {
        return new TaskResource($model->load('assignees'));
    }

    public function update(UpdateTaskRequest $request, Task $model): TaskResource
    {
        $model->update($request->validated());
        return new TaskResource($model);
    }

    public function destroy(Task $model): Response
    {
        $model->delete();
        return response()->noContent();
    }
}
