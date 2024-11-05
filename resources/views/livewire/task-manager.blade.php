<div>
    @if ($todoList)
        <h2>Tasks for: {{ $todoList->name }}</h2>
    @else
        <h2>No Todo List Selected</h2>
    @endif

    <form wire:submit.prevent="createTask">
        <div class="mb-3">
            <label for="taskName" class="form-label">Task Name</label>
            <input type="text" id="taskName" wire:model.defer="taskName" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="taskStatus" class="form-label">Status</label>
            <select id="taskStatus" wire:model.defer="taskStatus" class="form-control">
                <option value="Public">Public</option>
                <option value="Private">Private</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="taskProgress" class="form-label">Progress</label>
            <select id="taskProgress" wire:model.defer="taskProgress" class="form-control">
                <option value="New">New</option>
                <option value="In progress">In progress</option>
                <option value="Completed">Completed</option>
                <option value="Pause">Pause</option>
                <option value="Canceled">Canceled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Task</button>
    </form>

    @if ($tasks->isNotEmpty())
        <table class="table table-striped mt-5">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr>
                        @if($editingTaskId === $task->id)
                            <td><input type="text" wire:model.defer="taskName" class="form-control mb-2" required></td>
                            <td>
                                <select wire:model.defer="taskStatus" class="form-control mb-2">
                                    <option value="Public">Public</option>
                                    <option value="Private">Private</option>
                                </select>
                            </td>
                            <td>
                                <select wire:model.defer="taskProgress" class="form-control mb-2">
                                    <option value="New">New</option>
                                    <option value="In progress">In progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Pause">Pause</option>
                                    <option value="Canceled">Canceled</option>
                                </select>
                            </td>
                            <td>
                                <button wire:click="updateTask" class="btn btn-success">Save</button>
                                <button wire:click="cancelEditing" class="btn btn-secondary">Cancel</button>
                            </td>
                        @else
                            <td>{{ $task->name }}</td>
                            <td>{{ $task->status }}</td>
                            <td>{{ $task->progress }}</td>
                            <td>
                                <button wire:click="startEditing({{ $task->id }})" class="btn btn-primary btn-sm">Edit</button>
                                <button wire:click="deleteTask({{ $task->id }})" class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="mt-3">No tasks available. Please add a new task.</p>
    @endif
</div>