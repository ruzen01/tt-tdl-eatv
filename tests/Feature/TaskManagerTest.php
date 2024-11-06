<?php

namespace Tests\Feature;

use App\Livewire\TaskManager;
use App\Models\Task;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskManagerTest extends TestCase
    {
        use RefreshDatabase;

        private User $user;
        private TodoList $todoList;

        protected function setUp(): void
        {
            parent::setUp();

            $this->user = User::factory()->create();
            $this->todoList = TodoList::factory()->create([
                'user_id' => $this->user->id
            ]);
        }

        /** @test */
        public function it_shows_public_tasks_for_guests()
        {
            $publicTask = Task::factory()->create([
                'todo_list_id' => $this->todoList->id,
                'status' => 'Public'
            ]);

            $privateTask = Task::factory()->create([
                'todo_list_id' => $this->todoList->id,
                'status' => 'Private'
            ]);

            Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                ->assertSee($publicTask->name)
                ->assertDontSee($privateTask->name);
        }

        /** @test */
        public function it_shows_all_tasks_for_owner()
        {
            $this->actingAs($this->user);

            $publicTask = Task::factory()->create([
                'todo_list_id' => $this->todoList->id,
                'status' => 'Public'
            ]);

            $privateTask = Task::factory()->create([
                'todo_list_id' => $this->todoList->id,
                'status' => 'Private'
            ]);

            Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                ->assertSee($publicTask->name)
                ->assertSee($privateTask->name);
        }

        /** @test */
        public function owner_can_create_task()
        {
            $this->actingAs($this->user);

            Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                ->set('taskName', 'New Task')
                ->set('taskStatus', 'Public')
                ->set('taskProgress', 'New')
                ->call('createTask')
                ->assertHasNoErrors();

            $this->assertDatabaseHas('tasks', [
                'name' => 'New Task',
                'todo_list_id' => $this->todoList->id
            ]);
        }

        /** @test */
        public function non_owner_cannot_create_task()
        {
            $otherUser = User::factory()->create();
            $this->actingAs($otherUser);

            Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                ->set('taskName', 'New Task')
                ->set('taskStatus', 'Public')
                ->set('taskProgress', 'New')
                ->call('createTask');

            $this->assertDatabaseMissing('tasks', [
                'name' => 'New Task',
                'todo_list_id' => $this->todoList->id
            ]);
        }

        /** @test */

                /** @test */
                public function owner_can_edit_task()
                {
                    $this->actingAs($this->user);

                    $task = Task::factory()->create([
                        'todo_list_id' => $this->todoList->id,
                        'name' => 'Original Name'
                    ]);

                    Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                        ->call('startEditing', $task->id)
                        ->set('taskName', 'Updated Name')
                        ->set('taskStatus', 'Public')
                        ->set('taskProgress', 'Completed')
                        ->call('updateTask')
                        ->assertHasNoErrors();

                    $this->assertDatabaseHas('tasks', [
                        'id' => $task->id,
                        'name' => 'Updated Name',
                        'status' => 'Public',
                        'progress' => 'Completed'
                    ]);
                }

                /** @test */
        /** @test */
        public function nonOwnerCannotEditTask()
        {
            $otherUser = User::factory()->create();
            $this->actingAs($otherUser);

            $todoList = TodoList::factory()->create([
                'user_id' => $this->user->id
            ]);

            $task = Task::factory()->create([
                'todo_list_id' => $todoList->id,
                'name' => 'Original Name'
            ]);

            Livewire::test(TaskManager::class, ['todoListId' => $todoList->id])
                ->call('startEditing', $task->id)
                ->set('taskName', 'Updated Name')
                ->call('updateTask');

            $this->assertDatabaseHas('tasks', [
                'id' => $task->id,
                'name' => 'Original Name'
            ]);
        }

                /** @test */
                public function owner_can_delete_task()
                {
                    $this->actingAs($this->user);

                    $task = Task::factory()->create([
                        'todo_list_id' => $this->todoList->id
                    ]);

                    Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                        ->call('deleteTask', $task->id);

                    $this->assertDatabaseMissing('tasks', [
                        'id' => $task->id
                    ]);
                }

                /** @test */
                public function non_owner_cannot_delete_task()
                {
                    $otherUser = User::factory()->create();
                    $this->actingAs($otherUser);

                    $task = Task::factory()->create([
                        'todo_list_id' => $this->todoList->id
                    ]);

                    Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                        ->call('deleteTask', $task->id);

                    $this->assertDatabaseHas('tasks', [
                        'id' => $task->id
                    ]);
                }

                /** @test */
                public function it_validates_task_creation()
                {
                    $this->actingAs($this->user);

                    Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                        ->set('taskName', '')
                        ->set('taskStatus', 'Invalid')
                        ->set('taskProgress', 'Invalid')
                        ->call('createTask')
                        ->assertHasErrors([
                            'taskName' => 'required',
                            'taskStatus' => 'in',
                            'taskProgress' => 'in'
                        ]);
                }

                /** @test */
                            /** @test */
                            public function it_resets_form_after_task_creation()
                            {
                                $this->actingAs($this->user);

                                Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                                    ->set('taskName', 'New Task')
                                    ->set('taskStatus', 'Public')
                                    ->set('taskProgress', 'New')
                                    ->call('createTask')
                                    ->assertSet('taskName', '')
                                    ->assertSet('taskStatus', 'Public')
                                    ->assertSet('taskProgress', 'New');
                            }

                            /** @test */
        /** @test */
        public function it_can_cancel_editing()
        {
        $this->actingAs($this->user);

        $task = Task::factory()->create([
            'todo_list_id' => $this->todoList->id,
            'name' => 'Original Name'
        ]);

        $component = Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
            ->call('startEditing', $task->id)
            ->set('taskName', 'Updated Name')
            ->call('cancelEditing')
            ->assertSet('editingTaskId', null)
            ->assertSet('taskName', '');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Original Name'
        ]);
        }

                            /** @test */
                            public function it_shows_private_todo_list_only_to_owner()
                            {
                                $privateTodoList = TodoList::factory()->create([
                                    'user_id' => $this->user->id,
                                    'status' => 'Private'
                                ]);

                                $task = Task::factory()->create([
                                    'todo_list_id' => $privateTodoList->id,
                                    'status' => 'Public'
                                ]);

                                // Гость не должен видеть приватный список
                                Livewire::test(TaskManager::class, ['todoListId' => $privateTodoList->id])
                                    ->assertDontSee($task->name);

                                // Владелец должен видеть приватный список
                                $this->actingAs($this->user);
                                Livewire::test(TaskManager::class, ['todoListId' => $privateTodoList->id])
                                    ->assertSee($task->name);
                            }

                            /** @test */
        /** @test */
        public function it_requires_authentication_for_task_management()
        {
        $task = Task::factory()->create([
            'todo_list_id' => $this->todoList->id,
            'name' => 'Original Name'
        ]);

        $component = Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
            ->set('taskName', 'New Task')
            ->call('createTask');

        $this->assertDatabaseMissing('tasks', [
            'name' => 'New Task'
        ]);

        $component->set('taskStatus', 'Public')
            ->call('updateTask');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Original Name'
        ]);

        $component->call('deleteTask', $task->id);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id
        ]);
        }

                            /** @test */
                            public function it_validates_task_update()
                            {
                                $this->actingAs($this->user);

                                $task = Task::factory()->create([
                                    'todo_list_id' => $this->todoList->id
                                ]);

                                Livewire::test(TaskManager::class, ['todoListId' => $this->todoList->id])
                                    ->call('startEditing', $task->id)
                                    ->set('taskName', '')
                                    ->set('taskStatus', 'Invalid')
                                    ->set('taskProgress', 'Invalid')
                                    ->call('updateTask')
                                    ->assertHasErrors([
                                        'taskName' => 'required',
                                        'taskStatus' => 'in',
                                        'taskProgress' => 'in'
                                    ]);
                            }
                        }