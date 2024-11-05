<div class="row">
    <div class="col-md-6">
        <h1>Your Todo Lists</h1>

        <form wire:submit.prevent="createTodoList">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" wire:model.defer="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" wire:model.defer="status" class="form-control" required>
                    <option value="Public">Public</option>
                    <option value="Private">Private</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
        </form>

        <table class="table mt-5">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Author</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todolists as $todolist)
                    <tr>
                        @if($editingId === $todolist->id)
                            <td><input type="text" wire:model.defer="name" class="form-control"></td>
                            <td>
                                <select wire:model.defer="status" class="form-control">
                                    <option value="Public">Public</option>
                                    <option value="Private">Private</option>
                                </select>
                            </td>
                            <td>{{ $todolist->user ? $todolist->user->name : 'Unknown' }}</td>
                            <td>
                                <button wire:click="updateTodoList" class="btn btn-success">Save</button>
                                <button wire:click="cancelEditing" class="btn btn-secondary">Cancel</button>
                            </td>
                        @else
                            <td>
                                <a href="#" wire:click.prevent="selectTodoList({{ $todolist->id }})">
                                    {{ $todolist->name }}
                                </a>
                            </td>
                            <td>{{ $todolist->status }}</td>
                            <td>{{ $todolist->user ? $todolist->user->name : 'Unknown' }}</td>
                            <td>
                                <button wire:click="startEditing({{ $todolist->id }})" class="btn btn-primary">Edit</button>
                                <button wire:click="deleteTodoList({{ $todolist->id }})" class="btn btn-danger">Delete</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-6">
        @if ($selectedTodoList)
            <livewire:task-manager :todoListId="$selectedTodoList->id" key="{{ $selectedTodoList->id }}" />
        @endif
    </div>
</div>