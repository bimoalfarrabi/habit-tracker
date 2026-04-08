<?php

namespace Tests\Unit\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Services\StreakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreakServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_streak_for_user_counts_consecutive_days(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create();

        HabitLog::factory()->for($user)->for($habit)->create([
            'log_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        HabitLog::factory()->for($user)->for($habit)->create([
            'log_date' => now()->subDay()->toDateString(),
            'status' => 'completed',
        ]);

        $service = app(StreakService::class);

        $this->assertSame(2, $service->getCurrentStreakForUser($user));
    }

    public function test_longest_streak_for_habit(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create();

        HabitLog::factory()->for($user)->for($habit)->create([
            'log_date' => now()->subDays(5)->toDateString(),
            'status' => 'completed',
        ]);
        HabitLog::factory()->for($user)->for($habit)->create([
            'log_date' => now()->subDays(4)->toDateString(),
            'status' => 'completed',
        ]);
        HabitLog::factory()->for($user)->for($habit)->create([
            'log_date' => now()->subDays(2)->toDateString(),
            'status' => 'completed',
        ]);
        HabitLog::factory()->for($user)->for($habit)->create([
            'log_date' => now()->subDay()->toDateString(),
            'status' => 'completed',
        ]);
        HabitLog::factory()->for($user)->for($habit)->create([
            'log_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $service = app(StreakService::class);

        $this->assertSame(3, $service->getLongestStreakForHabit($habit));
    }
}
