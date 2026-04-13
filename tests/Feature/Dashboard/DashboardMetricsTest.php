<?php

namespace Tests\Feature\Dashboard;

use App\Models\FocusSession;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\Todo;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_summary_returns_expected_metric_shape(): void
    {
        $user = User::factory()->create();

        $habit = Habit::factory()->for($user)->create([
            'is_active' => true,
            'archived_at' => null,
        ]);

        HabitLog::factory()->for($user)->for($habit)->create([
            'log_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        FocusSession::factory()->for($user)->create([
            'session_date' => now()->toDateString(),
            'focused_duration_seconds' => 1800,
            'unfocused_duration_seconds' => 120,
            'total_duration_seconds' => 1920,
        ]);

        UserNotification::factory()->for($user)->count(2)->create(['is_read' => false]);
        Todo::factory()->for($user)->create([
            'is_completed' => false,
            'due_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->getJson(route('ajax.dashboard.summary'));

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_active_habits', 1)
            ->assertJsonPath('data.completed_today', 1)
            ->assertJsonPath('data.focus_minutes_today', 30)
            ->assertJsonPath('data.unread_notifications', 2)
            ->assertJsonPath('data.pending_todos', 1)
            ->assertJsonPath('data.due_today_todos', 1);
    }
}
