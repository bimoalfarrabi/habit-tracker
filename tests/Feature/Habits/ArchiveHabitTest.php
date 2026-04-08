<?php

namespace Tests\Feature\Habits;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveHabitTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_archive_habit(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create([
            'is_active' => true,
            'archived_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('habits.archive', $habit));

        $response->assertRedirect(route('habits.index'));

        $habit->refresh();

        $this->assertFalse($habit->is_active);
        $this->assertNotNull($habit->archived_at);
    }
}
