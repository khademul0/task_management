<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement([TaskStatus::PENDING->value, TaskStatus::IN_PROGRESS->value, TaskStatus::COMPLETED->value]),
        ];
    }
}
