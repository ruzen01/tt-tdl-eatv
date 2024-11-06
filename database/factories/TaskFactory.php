<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(4),
            'status' => $this->faker->randomElement(['Public', 'Private']),
            'progress' => $this->faker->randomElement(['New', 'Completed', 'In progress', 'Pause', 'Canceled']),
            'todo_list_id' => TodoList::factory()
        ];
    }
}