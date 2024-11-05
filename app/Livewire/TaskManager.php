<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TodoList;
use App\Models\Task;

class TaskManager extends Component
{
    
    public $todoListId;
    public $taskName;
    public $taskStatus = 'Public';
    public $taskProgress = 'New';
    public $editingTaskId;

    // protected $listeners = ['selectTodoList'];

    public function render()
    {
        $todoList = TodoList::find($this->todoListId);
        $tasks = $todoList ? $todoList->tasks : collect();

        return view('livewire.task-manager', [
            'todoList' => $todoList,
            'tasks' => $tasks,
        ]);
    }

    public function createTask()
    {
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
        $this->taskName = $task->name;
        $this->taskStatus = $task->status;
        $this->taskProgress = $task->progress;
        $this->editingTaskId = $taskId;
    }

    public function updateTask()
    {
        $this->validate([
            'taskName' => 'required|string|max:255',
            'taskStatus' => 'required|in:Public,Private',
            'taskProgress' => 'required|in:New,Completed,In progress,Pause,Canceled',
        ]);

        $task = Task::find($this->editingTaskId);
        $task->update([
            'name' => $this->taskName,
            'status' => $this->taskStatus,
            'progress' => $this->taskProgress,
        ]);

        $this->reset(['taskName', 'taskStatus', 'taskProgress', 'editingTaskId']);
    }

    public function deleteTask($taskId)
    {
        Task::find($taskId)->delete();
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
