<?php

namespace Tests\Unit\Services;

use App\Models\Habit;
use App\Models\User;
use App\Services\HabitService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_for_user_applies_defaults(): void
    {
        $user = User::factory()->create();
        $service = app(HabitService::class);

        $habit = $service->createForUser($user, [
            'title' => 'Morning Walk',
            'frequency' => 'daily',
        ]);

        $this->assertSame($user->id, $habit->user_id);
        $this->assertTrue($habit->is_active);
        $this->assertSame(1, $habit->target_count);
    }

    public function test_archive_sets_archived_state(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create([
            'is_active' => true,
            'archived_at' => null,
        ]);

        $service = app(HabitService::class);
        $updated = $service->archiveForUser($user, $habit);

        $this->assertFalse($updated->is_active);
        $this->assertNotNull($updated->archived_at);
    }

    public function test_update_throws_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $habit = Habit::factory()->for($owner)->create();

        $service = app(HabitService::class);

        $this->expectException(AuthorizationException::class);

        $service->updateForUser($intruder, $habit, [
            'title' => 'Hacked',
        ]);
    }
}
