<?php

namespace Tests\Feature\Habits;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateHabitTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_own_habit(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create([
            'title' => 'Read 10 pages',
            'frequency' => 'daily',
        ]);

        $response = $this->actingAs($user)->put(route('habits.update', $habit), [
            'title' => 'Read 20 pages',
            'description' => 'Night reading',
            'frequency' => 'daily',
            'target_count' => 20,
            'reminder_time' => '21:00',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('habits.index'));

        $this->assertDatabaseHas('habits', [
            'id' => $habit->id,
            'title' => 'Read 20 pages',
            'target_count' => 20,
        ]);
    }

    public function test_user_cannot_update_other_user_habit(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $habit = Habit::factory()->for($owner)->create();

        $response = $this->actingAs($intruder)->put(route('habits.update', $habit), [
            'title' => 'Tampered',
            'frequency' => 'daily',
            'target_count' => 1,
        ]);

        $response->assertStatus(403);
    }
}
