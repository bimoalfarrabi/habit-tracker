<?php

namespace Tests\Feature\HabitLogs;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickCheckinTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_checkin_creates_then_updates_same_day_log(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create();

        $firstResponse = $this->actingAs($user)->postJson(route('ajax.habit-logs.quick-checkin'), [
            'habit_id' => $habit->id,
            'status' => 'completed',
            'qty' => 1,
        ]);

        $firstResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.action', 'created');

        $secondResponse = $this->actingAs($user)->postJson(route('ajax.habit-logs.quick-checkin'), [
            'habit_id' => $habit->id,
            'status' => 'completed',
            'qty' => 2,
        ]);

        $secondResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.action', 'updated');

        $this->assertSame(1, HabitLog::query()->count());

        $this->assertDatabaseHas('habit_logs', [
            'habit_id' => $habit->id,
            'qty' => 2,
            'status' => 'completed',
        ]);
    }
}
