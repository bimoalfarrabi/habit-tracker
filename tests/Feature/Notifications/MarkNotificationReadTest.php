<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkNotificationReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_own_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = UserNotification::factory()->for($user)->create([
            'is_read' => false,
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('notifications.read', $notification));

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.notification_id', $notification->id)
            ->assertJsonPath('data.is_read', true);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }
}
