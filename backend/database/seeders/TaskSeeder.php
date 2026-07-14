<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::factory()
            ->count(20)
            ->create([
                'status' => TaskStatus::TODO,
            ]);

        Task::factory()
            ->count(15)
            ->create([
                'status' => TaskStatus::IN_PROGRESS,
            ]);

        Task::factory()
            ->count(20)
            ->create([
                'status' => TaskStatus::DONE,
            ]);

        Task::factory()
            ->count(5)
            ->create([
                'status' => TaskStatus::CANCELLED,
            ]);

        Task::factory()
            ->count(10)
            ->create([
                'priority' => TaskPriority::URGENT,
            ]);

        Task::factory()
            ->count(30)
            ->create();
    }
}
