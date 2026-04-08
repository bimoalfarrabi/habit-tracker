<?php

namespace Tests\Feature\FocusSessions;

use App\Models\FocusSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartFocusSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_focus_session_reuses_existing_running_session(): void
    {
        $user = User::factory()->create();

        $runningSession = FocusSession::factory()->for($user)->create([
            'status' => 'running',
            'end_time' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('ajax.focus-sessions.start'), [
            'planned_duration_minutes' => 25,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.reused_existing_session', true)
            ->assertJsonPath('data.session.id', $runningSession->id);

        $this->assertSame(1, FocusSession::query()->where('user_id', $user->id)->count());
    }
}
