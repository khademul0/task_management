<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Task;
use App\Enums\TaskStatus;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;

class TaskManager extends Component
{
    #[Validate('required|min:3|max:255')]
    public $title = '';

    #[Validate('nullable|string')]
    public $description = '';

    public $taskId = null;
    public $isEditing = false;

    public function render()
    {
        return view('livewire.task-manager', [
            'tasks' => Auth::user()->tasks()->latest()->get()
        ]);
    }

    public function saveTask()
    {
        $this->validate();

        if ($this->isEditing) {
            $task = Auth::user()->tasks()->findOrFail($this->taskId);
            $task->update([
                'title' => $this->title,
                'description' => $this->description,
            ]);
            $this->isEditing = false;
        } else {
            Auth::user()->tasks()->create([
                'title' => $this->title,
                'description' => $this->description,
                'status' => TaskStatus::PENDING,
            ]);
        }

        $this->reset(['title', 'description', 'taskId', 'isEditing']);
    }

    public function editTask($id)
    {
        $task = Auth::user()->tasks()->findOrFail($id);
        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->isEditing = true;
    }

    public function cancelEdit()
    {
        $this->reset(['title', 'description', 'taskId', 'isEditing']);
    }

    public function updateStatus($id, $status)
    {
        $task = Auth::user()->tasks()->findOrFail($id);
        $task->update(['status' => $status]);
    }

    public function deleteTask($id)
    {
        Auth::user()->tasks()->findOrFail($id)->delete();
        if ($this->isEditing && $this->taskId === $id) {
            $this->cancelEdit();
        }
    }
}
