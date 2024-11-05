<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'progress', 'todo_list_id'];

    public function todoList()
    {
        return $this->belongsTo(TodoList::class);
    }
}
