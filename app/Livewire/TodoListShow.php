<?php

namespace App\Livewire;

use App\Models\TodoList;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TodoListShow extends Component
{
    public TodoList $todoList;

    public function mount(TodoList $todoList)
    {
        if ($todoList->status !== 'Public' &&
            (! Auth::check() || Auth::id() !== $todoList->user_id)) {
            abort(403, 'This todo list is private');
        }

        $this->todoList = $todoList->load(['tasks', 'user']);
    }

    public function render()
    {
        return view('livewire.todo-list-show')
            ->extends('layouts.app')
            ->section('content');
    }
}
