<?php

namespace Tests\Feature\FocusSessions;

use App\Models\FocusSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StopFocusSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_stop_focus_session_updates_durations_and_status(): void
    {
        $user = User::factory()->create();

        $session = FocusSession::factory()->for($user)->create([
            'status' => 'running',
            'end_time' => null,
            'total_duration_seconds' => 0,
            'focused_duration_seconds' => 0,
            'unfocused_duration_seconds' => 0,
        ]);

        $response = $this->actingAs($user)->postJson(route('ajax.focus-sessions.stop', $session), [
            'focused_duration_seconds' => 1200,
            'unfocused_duration_seconds' => 300,
            'interruption_count' => 2,
            'status' => 'completed',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.session.total_duration_seconds', 1500)
            ->assertJsonPath('data.session.status', 'completed');

        $this->assertDatabaseHas('focus_sessions', [
            'id' => $session->id,
            'status' => 'completed',
            'total_duration_seconds' => 1500,
            'focused_duration_seconds' => 1200,
            'unfocused_duration_seconds' => 300,
            'interruption_count' => 2,
        ]);
    }
}
