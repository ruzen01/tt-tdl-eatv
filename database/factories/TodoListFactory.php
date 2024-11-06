<?php

namespace Database\Factories;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoListFactory extends Factory
{
    protected $model = TodoList::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['Public', 'Private']),
            'user_id' => User::factory(),
        ];
    }
}
