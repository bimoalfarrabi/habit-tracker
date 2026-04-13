<?php

namespace Tests\Feature\Todos;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToggleTodoCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_own_todo_as_completed(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->for($user)->create([
            'is_completed' => false,
            'completed_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('todos.toggle-completion', $todo), [
            'is_completed' => 1,
        ]);

        $response->assertRedirect();

        $todo->refresh();

        $this->assertTrue($todo->is_completed);
        $this->assertNotNull($todo->completed_at);
    }

    public function test_user_cannot_toggle_other_user_todo(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()->for($otherUser)->create([
            'is_completed' => false,
        ]);

        $response = $this->actingAs($user)->post(route('todos.toggle-completion', $todo), [
            'is_completed' => 1,
        ]);

        $response->assertForbidden();
    }
}
