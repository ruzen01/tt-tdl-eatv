<?php

namespace App\Livewire;

use App\Models\TodoList;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TodoListManager extends Component
{
    public $name;
    public $status = 'Private';
    public $editingId;
    public $selectedTodoList = null;

    public function render()
    {
        // Получаем публичные списки
        $query = TodoList::where('status', 'Public');

        // Если пользователь авторизован, добавляем его приватные списки
        if (Auth::check()) {
            $query->orWhere(function ($q) {
                $q->where('status', 'Private')
                  ->where('user_id', Auth::id());
            });
        }

        $todolists = $query->with('user')->get();

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

        // Проверяем, является ли пользователь владельцем
        if ($todo->user_id !== Auth::id()) {
            return;
        }

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

        // Проверяем, является ли пользователь владельцем
        if ($todo->user_id !== Auth::id()) {
            return;
        }

        $todo->update([
            'name' => $this->name,
            'status' => $this->status,
        ]);

        $this->reset(['name', 'status', 'editingId']);
    }

    public function deleteTodoList($id)
    {
        $todo = TodoList::findOrFail($id);

        // Проверяем, является ли пользователь владельцем
        if ($todo->user_id !== Auth::id()) {
            return;
        }

        $todo->delete();
        $this->reset(['name', 'status', 'editingId']);
    }

    public function selectTodoList($id)
    {
        $todoList = TodoList::findOrFail($id);

        // Проверяем доступ к списку
        if ($todoList->status === 'Private' && (! Auth::check() || Auth::id() !== $todoList->user_id)) {
            session()->flash('error', 'You do not have access to this private list.');

            return;
        }

        $this->selectedTodoList = $todoList;
    }
}
