<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_task()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('task-manager')
            ->set('title', 'Test Task')
            ->set('description', 'Test Description')
            ->call('saveTask');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => TaskStatus::PENDING->value,
        ]);
    }

    public function test_user_can_update_a_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'title' => 'Old Title']);

        Livewire::actingAs($user)
            ->test('task-manager')
            ->call('editTask', $task->id)
            ->set('title', 'New Title')
            ->call('saveTask');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'New Title',
        ]);
    }

    public function test_user_can_update_task_status()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => TaskStatus::PENDING->value]);

        Livewire::actingAs($user)
            ->test('task-manager')
            ->call('updateStatus', $task->id, TaskStatus::IN_PROGRESS->value);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => TaskStatus::IN_PROGRESS->value,
        ]);
    }

    public function test_user_can_delete_a_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test('task-manager')
            ->call('deleteTask', $task->id);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_see_or_modify_other_users_tasks()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user2->id]);

        $component = Livewire::actingAs($user1)
            ->test('task-manager');

        $component->assertDontSee($task->title);

        // Expect Exception when attempting to edit another user's task
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $component->call('editTask', $task->id);
    }
}
