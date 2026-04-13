<?php

namespace Tests\Feature\Todos;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_todo(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('todos.store'), [
            'title' => 'Submit weekly report',
            'description' => 'Wrap up metrics and notes',
            'due_date' => now()->addDay()->toDateString(),
            'reminder_time' => '09:30',
            'priority' => 'high',
        ]);

        $response->assertRedirect(route('todos.index'));

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'title' => 'Submit weekly report',
            'priority' => 'high',
            'is_completed' => false,
        ]);

        $this->assertSame(1, Todo::query()->count());
    }
}
