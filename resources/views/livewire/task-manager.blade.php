<div>  
    @if ($todoList)  
        <h2>Tasks for: {{ $todoList->name }}</h2>  
    @else  
        <h2>No Todo List Selected</h2>  
    @endif  

    @auth
    @if(Auth::id() === $todoList->user_id) <!-- Показываем форму только владельцу списка -->
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
    @endif
    @endauth 

    @if (session()->has('error'))
        <div class="alert alert-danger mt-3">
            {{ session('error') }}
        </div>
    @endif

    @if ($tasks->isNotEmpty())  
        <table class="table table-striped mt-5">  
            <thead>  
                <tr>  
                    <th>Name</th>  
                    <th>Status</th>  
                    <th>Progress</th>  
                    @auth 
                    @if(Auth::id() === $todoList->user_id)
                    <th>Actions</th>  
                    @endif
                    @endauth 
                </tr>  
            </thead>  
            <tbody>  
                @foreach($tasks as $task)  
                    @if($task->status === 'Public' || (Auth::check() && Auth::id() === $todoList->user_id))
                    <tr>  
                        @if(Auth::check() && $editingTaskId === $task->id && Auth::id() === $todoList->user_id)  
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
                            @auth 
                            @if(Auth::id() === $todoList->user_id)
                            <td>  
                                <button wire:click="startEditing({{ $task->id }})" class="btn btn-primary btn-sm">Edit</button>  
                                <button wire:click="deleteTask({{ $task->id }})" class="btn btn-danger btn-sm">Delete</button>
                            </td>  
                            @endif
                            @endauth 
                        @endif  
                    </tr>
                    @endif  
                @endforeach  
            </tbody>  
        </table>  
    @else  
        <p class="mt-3">
            @if(Auth::check() && Auth::id() === $todoList->user_id)
                No tasks available. Please add a new task.
            @else
                No public tasks available in this list.
            @endif
        </p>
    @endif  
</div>