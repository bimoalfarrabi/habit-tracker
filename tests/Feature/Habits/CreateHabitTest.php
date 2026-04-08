<?php

namespace Tests\Feature\Habits;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateHabitTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_habit(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('habits.store'), [
            'title' => 'Drink Water',
            'description' => 'At least 8 glasses',
            'frequency' => 'daily',
            'target_count' => 8,
            'reminder_time' => '08:00',
        ]);

        $response->assertRedirect(route('habits.index'));

        $this->assertDatabaseHas('habits', [
            'user_id' => $user->id,
            'title' => 'Drink Water',
            'frequency' => 'daily',
            'target_count' => 8,
            'is_active' => true,
        ]);

        $this->assertSame(1, Habit::query()->count());
    }
}
