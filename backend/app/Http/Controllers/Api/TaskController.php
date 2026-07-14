<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TaskResource::collection(Task::query()->with('createdBy')->get());
    }

    public function store(StoreTaskRequest $request): TaskResource
    {
        $model = Task::create($request->validated());
        return new TaskResource($model);
    }

    public function show(Task $model): TaskResource
    {
        return new TaskResource($model);
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
