<div class="space-y-6">
    <!-- Task Form -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ $isEditing ? 'Edit Task' : 'Create New Task' }}</h2>
        <form wire:submit="saveTask" class="space-y-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" id="title" wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="What needs to be done?">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                <textarea id="description" wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Add some details..."></textarea>
                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    {{ $isEditing ? 'Update Task' : 'Save Task' }}
                </button>
                @if($isEditing)
                    <button type="button" wire:click="cancelEdit" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Cancel
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Tasks List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-lg font-semibold text-gray-900">Your Tasks</h2>
            <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $tasks->count() }} total</span>
        </div>
        
        @if($tasks->isEmpty())
            <div class="p-12 text-center text-gray-500 flex flex-col items-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <p class="text-base font-medium">No tasks yet.</p>
                <p class="text-sm mt-1">Create your first task above to get started!</p>
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach($tasks as $task)
                    <li class="p-6 hover:bg-gray-50 transition duration-150 ease-in-out flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-base font-semibold {{ $task->status === App\Enums\TaskStatus::COMPLETED ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                    {{ $task->title }}
                                </h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $task->status->color() }}-100 text-{{ $task->status->color() }}-800 border border-{{ $task->status->color() }}-200">
                                    {{ $task->status->label() }}
                                </span>
                            </div>
                            @if($task->description)
                                <p class="text-sm text-gray-500 mt-1 {{ $task->status === App\Enums\TaskStatus::COMPLETED ? 'line-through opacity-70' : '' }}">{{ $task->description }}</p>
                            @endif
                            <div class="text-xs text-gray-400 mt-2 font-medium">
                                Created {{ $task->created_at->diffForHumans() }}
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 shrink-0">
                            <select wire:change="updateStatus({{ $task->id }}, $event.target.value)" class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8">
                                @foreach(App\Enums\TaskStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @if($task->status === $status) selected @endif>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>

                            <button wire:click="editTask({{ $task->id }})" class="p-2 text-gray-400 hover:text-indigo-600 transition" title="Edit Task">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            <button wire:confirm="Are you sure you want to delete this task?" wire:click="deleteTask({{ $task->id }})" class="p-2 text-gray-400 hover:text-red-600 transition" title="Delete Task">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
