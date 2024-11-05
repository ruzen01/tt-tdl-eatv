<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TodoList;
use Illuminate\Support\Facades\Auth;

class TodoListManager extends Component
{
    public $name;
    public $status = 'Private';
    public $editingId;
    public $selectedTodoList = null;

    public function render()
    {
        $todolists = TodoList::with('user')->get();
        return view('livewire.todo-list-manager', ['todolists' => $todolists]);
    }

    public function createTodoList()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Public,Private',
        ]);

        Auth::user()->todolists()->create([
            'name' => $this->name,
            'status' => $this->status,
        ]);

        $this->reset(['name', 'status']);
    }

    public function startEditing($id)
    {
        $todo = TodoList::findOrFail($id);
        $this->name = $todo->name;
        $this->status = $todo->status;
        $this->editingId = $id;
    }

    public function cancelEditing()
    {
        $this->reset(['name', 'status', 'editingId']);
    }

    public function updateTodoList()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Public,Private',
        ]);

        $todo = TodoList::findOrFail($this->editingId);
        $todo->update([
            'name' => $this->name,
            'status' => $this->status,
        ]);

        $this->reset(['name', 'status', 'editingId']);
    }

    public function deleteTodoList($id)
    {
        TodoList::findOrFail($id)->delete();
        $this->reset(['name', 'status', 'editingId']);
    }

    public function selectTodoList($id)
    {
        $this->selectedTodoList = TodoList::findOrFail($id);
    }
}