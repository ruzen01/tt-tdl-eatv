<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskManager extends Component
{
    public $todoListId;
    public $taskName;
    public $taskStatus = 'Public';
    public $taskProgress = 'New';
    public $editingTaskId;

    public function render()
    {
        $todoList = TodoList::find($this->todoListId);

        // Проверяем доступ к списку
        if ($todoList->status === 'Private' && (! Auth::check() || Auth::id() !== $todoList->user_id)) {
            return view('livewire.task-manager', [
                'todoList' => null,
                'tasks' => collect(),
            ]);
        }

        // Получаем публичные задачи
        $query = Task::where('todo_list_id', $this->todoListId)
            ->where('status', 'Public');

        // Если пользователь авторизован и является владельцем списка,
        // добавляем его приватные задачи
        if (Auth::check() && $todoList->user_id === Auth::id()) {
            $query->orWhere(function ($q) {
                $q->where('todo_list_id', $this->todoListId)
                    ->where('status', 'Private');
            });
        }

        $tasks = $query->get();

        return view('livewire.task-manager', [
            'todoList' => $todoList,
            'tasks' => $tasks,
        ]);
    }

    public function createTask()
    {
        $todoList = TodoList::findOrFail($this->todoListId);

        // Проверяем, является ли пользователь владельцем списка
        if (! Auth::check() || Auth::id() !== $todoList->user_id) {
            session()->flash('error', 'You do not have permission to create tasks in this list.');

            return;
        }

        $this->validate([
            'taskName' => 'required|string|max:255',
            'taskStatus' => 'required|in:Public,Private',
            'taskProgress' => 'required|in:New,Completed,In progress,Pause,Canceled',
        ]);

        Task::create([
            'name' => $this->taskName,
            'status' => $this->taskStatus,
            'progress' => $this->taskProgress,
            'todo_list_id' => $this->todoListId,
        ]);

        $this->reset(['taskName', 'taskStatus', 'taskProgress']);
    }

    public function startEditing($taskId)
    {
        $task = Task::findOrFail($taskId);
        $todoList = TodoList::findOrFail($task->todo_list_id);

        // Проверяем права на редактирование
        if (! Auth::check() || Auth::id() !== $todoList->user_id) {
            return;
        }

        $this->taskName = $task->name;
        $this->taskStatus = $task->status;
        $this->taskProgress = $task->progress;
        $this->editingTaskId = $taskId;
    }

    public function updateTask()
    {
        $task = Task::findOrFail($this->editingTaskId);
        $todoList = TodoList::findOrFail($task->todo_list_id);

        // Проверяем права на обновление
        if (! Auth::check() || Auth::id() !== $todoList->user_id) {
            return;
        }

        $this->validate([
            'taskName' => 'required|string|max:255',
            'taskStatus' => 'required|in:Public,Private',
            'taskProgress' => 'required|in:New,Completed,In progress,Pause,Canceled',
        ]);

        $task->update([
            'name' => $this->taskName,
            'status' => $this->taskStatus,
            'progress' => $this->taskProgress,
        ]);

        $this->reset(['taskName', 'taskStatus', 'taskProgress', 'editingTaskId']);
    }

    public function deleteTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        $todoList = TodoList::findOrFail($task->todo_list_id);

        // Проверяем права на удаление
        if (! Auth::check() || Auth::id() !== $todoList->user_id) {
            return;
        }

        $task->delete();
    }

    public function cancelEditing()
    {
        $this->reset(['taskName', 'taskStatus', 'taskProgress', 'editingTaskId']);
    }

    protected $listeners = ['todoListUpdated' => 'updateTodoListName'];

    public function updateTodoListName($updatedList)
    {
        if ($this->todoListId == $updatedList['id']) {
            $this->todoListId = $updatedList['id'];
            $this->todoListName = $updatedList['name'];
        }
    }

    public function reloadTodoList($listId)
    {
        if ($this->todoListId == $listId) {
            $this->todoListId = TodoList::find($listId)->id;
        }
    }
}
