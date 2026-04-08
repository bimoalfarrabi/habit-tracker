<?php

namespace Tests\Unit\Services;

use App\Models\FocusSession;
use App\Models\User;
use App\Services\FocusSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class FocusSessionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_reuses_running_session(): void
    {
        $user = User::factory()->create();

        $running = FocusSession::factory()->for($user)->create([
            'status' => 'running',
            'end_time' => null,
        ]);

        $service = app(FocusSessionService::class);
        $result = $service->start($user, []);

        $this->assertTrue($result['reused_existing_session']);
        $this->assertSame($running->id, $result['session']->id);
    }

    public function test_stop_throws_when_session_not_running(): void
    {
        $user = User::factory()->create();
        $session = FocusSession::factory()->for($user)->create([
            'status' => 'completed',
        ]);

        $service = app(FocusSessionService::class);

        $this->expectException(InvalidArgumentException::class);

        $service->stop($user, $session, [
            'focused_duration_seconds' => 120,
            'unfocused_duration_seconds' => 30,
            'interruption_count' => 1,
            'status' => 'completed',
        ]);
    }

    public function test_stop_sets_total_duration_as_sum(): void
    {
        $user = User::factory()->create();
        $session = FocusSession::factory()->for($user)->create([
            'status' => 'running',
            'end_time' => null,
            'total_duration_seconds' => 0,
            'focused_duration_seconds' => 0,
            'unfocused_duration_seconds' => 0,
        ]);

        $service = app(FocusSessionService::class);

        $updated = $service->stop($user, $session, [
            'focused_duration_seconds' => 400,
            'unfocused_duration_seconds' => 60,
            'interruption_count' => 0,
            'status' => 'completed',
        ]);

        $this->assertSame(460, $updated->total_duration_seconds);
    }
}
