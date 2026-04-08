<?php

namespace Tests\Unit\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Services\HabitLogService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitLogServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_for_user_uses_update_or_create_for_same_day(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create();
        $service = app(HabitLogService::class);

        $firstLog = $service->storeForUser($user, [
            'habit_id' => $habit->id,
            'log_date' => now()->toDateString(),
            'status' => 'completed',
            'qty' => 1,
        ]);

        $secondLog = $service->storeForUser($user, [
            'habit_id' => $habit->id,
            'log_date' => now()->toDateString(),
            'status' => 'completed',
            'qty' => 3,
        ]);

        $this->assertSame($firstLog->id, $secondLog->id);
        $this->assertSame(1, HabitLog::query()->count());
        $this->assertDatabaseHas('habit_logs', [
            'id' => $firstLog->id,
            'qty' => 3,
        ]);
    }

    public function test_store_for_user_throws_if_habit_not_owned(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $habit = Habit::factory()->for($owner)->create();

        $service = app(HabitLogService::class);

        $this->expectException(AuthorizationException::class);

        $service->storeForUser($intruder, [
            'habit_id' => $habit->id,
            'log_date' => now()->toDateString(),
            'status' => 'completed',
        ]);
    }
}
