<?php

namespace Tests\Unit\Services;

use App\Models\FocusSession;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\Todo;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\DashboardStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_for_user_returns_expected_metrics(): void
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
            'focused_duration_seconds' => 1200,
            'unfocused_duration_seconds' => 100,
            'total_duration_seconds' => 1300,
        ]);

        UserNotification::factory()->for($user)->create([
            'is_read' => false,
        ]);

        Todo::factory()->for($user)->create([
            'is_completed' => false,
            'due_date' => now()->toDateString(),
        ]);

        Todo::factory()->for($user)->create([
            'is_completed' => true,
            'completed_at' => now(),
            'due_date' => now()->toDateString(),
        ]);

        $service = app(DashboardStatsService::class);
        $stats = $service->getForUser($user);

        $this->assertSame(1, $stats['total_active_habits']);
        $this->assertSame(1, $stats['completed_today']);
        $this->assertSame(20, $stats['focus_minutes_today']);
        $this->assertSame(1, $stats['unread_notifications']);
        $this->assertSame(1, $stats['pending_todos']);
        $this->assertSame(1, $stats['due_today_todos']);
    }
}
