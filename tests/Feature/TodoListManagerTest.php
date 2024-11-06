<?php

namespace Tests\Feature;

use App\Livewire\TodoListManager;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TodoListManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function showsPublicListsToGuestUsers()
    {
        $publicList = TodoList::factory()->create(['status' => 'Public']);
        $privateList = TodoList::factory()->create(['status' => 'Private']);

        Livewire::test(TodoListManager::class)
            ->assertSee($publicList->name)
            ->assertDontSee($privateList->name);
    }

    /** @test */
    public function authenticatedUserCanSeePublicAndOwnPrivateLists()
    {
        $publicList = TodoList::factory()->create(['status' => 'Public']);
        $ownPrivateList = TodoList::factory()->create([
            'status' => 'Private',
            'user_id' => $this->user->id,
        ]);
        $otherPrivateList = TodoList::factory()->create(['status' => 'Private']);

        Livewire::actingAs($this->user)
            ->test(TodoListManager::class)
            ->assertSee($publicList->name)
            ->assertSee($ownPrivateList->name)
            ->assertDontSee($otherPrivateList->name);
    }

    /** @test */
    public function canCreateTodoList()
    {
        Livewire::actingAs($this->user)
            ->test(TodoListManager::class)
            ->set('name', 'Test List')
            ->set('status', 'Private')
            ->call('createTodoList')
            ->assertHasNoErrors()
            ->assertSet('name', '')
            ->assertSet('status', 'Private');

        $this->assertDatabaseHas('todo_lists', [
            'name' => 'Test List',
            'status' => 'Private',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function validatesTodoListCreation()
    {
        Livewire::actingAs($this->user)
            ->test(TodoListManager::class)
            ->set('name', '')
            ->set('status', 'Invalid')
            ->call('createTodoList')
            ->assertHasErrors(['name', 'status']);
    }

    /** @test */
    public function canStartEditingOwnTodoList()
    {
        $todoList = TodoList::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'status' => 'Private',
        ]);

        Livewire::actingAs($this->user)
            ->test(TodoListManager::class)
            ->call('startEditing', $todoList->id)
            ->assertSet('name', 'Original Name')
            ->assertSet('status', 'Private')
            ->assertSet('editingId', $todoList->id);
    }

    /** @test */
    public function cannotEditOthersTodoList()
    {
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Original Name',
        ]);

        Livewire::actingAs($this->user)
            ->test(TodoListManager::class)
            ->call('startEditing', $todoList->id)
            ->assertSet('editingId', null);
    }

    /** @test */
    public function canUpdateOwnTodoList()
    {
        $todoList = TodoList::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
        ]);

        Livewire::actingAs($this->user)
            ->test(TodoListManager::class)
            ->set('editingId', $todoList->id)
            ->set('name', 'Updated Name')
            ->set('status', 'Public')
            ->call('updateTodoList')
            ->assertSet('editingId', null);

        $this->assertDatabaseHas('todo_lists', [
            'id' => $todoList->id,
            'name' => 'Updated Name',
            'status' => 'Public',
        ]);
    }

    /** @test */
    public function canDeleteOwnTodoList()
    {
        $todoList = TodoList::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(TodoListManager::class)
            ->call('deleteTodoList', $todoList->id);

        $this->assertDatabaseMissing('todo_lists', ['id' => $todoList->id]);
    }

    /** @test */
    public function cannotDeleteOthersTodoList()
    {
        $otherUser = User::factory()->create();
        $todoList = TodoList::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(TodoListManager::class)
            ->call('deleteTodoList', $todoList->id);

        $this->assertDatabaseHas('todo_lists', ['id' => $todoList->id]);
    }

    /** @test */
    public function canSelectPublicTodoList()
    {
        $publicList = TodoList::factory()->create(['status' => 'Public']);

        Livewire::test(TodoListManager::class)
            ->call('selectTodoList', $publicList->id)
            ->assertSet('selectedTodoList.id', $publicList->id);
    }

    /** @test */
    public function cannotSelectPrivateTodoListAsGuest()
    {
        $privateList = TodoList::factory()->create(['status' => 'Private']);

        Livewire::test(TodoListManager::class)
            ->call('selectTodoList', $privateList->id)
            ->assertSet('selectedTodoList', null);
    }
}
