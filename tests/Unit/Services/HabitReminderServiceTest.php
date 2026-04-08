<?php

namespace Tests\Unit\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\HabitReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitReminderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_run_does_not_create_duplicate_reminders_for_same_day(): void
    {
        $user = User::factory()->create();

        Habit::factory()->for($user)->create([
            'is_active' => true,
            'archived_at' => null,
            'reminder_time' => now()->format('H:i:s'),
            'title' => 'Hydrate',
        ]);

        $service = app(HabitReminderService::class);

        $service->run();
        $service->run();

        $this->assertSame(1, UserNotification::query()->where('type', 'habit_reminder')->count());
    }

    public function test_run_skips_habit_already_completed_today(): void
    {
        $user = User::factory()->create();

        $habit = Habit::factory()->for($user)->create([
            'is_active' => true,
            'archived_at' => null,
            'reminder_time' => now()->format('H:i:s'),
        ]);

        HabitLog::factory()->for($user)->for($habit)->create([
            'log_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $service = app(HabitReminderService::class);
        $service->run();

        $this->assertSame(0, UserNotification::query()->count());
    }
}
