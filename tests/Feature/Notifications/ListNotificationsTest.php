<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_list_is_scoped_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        UserNotification::factory()->for($user)->count(2)->create();
        UserNotification::factory()->for($otherUser)->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('ajax.notifications.list'));

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data.notifications');

        $this->assertEquals(2, $response->json('data.unread_count'));
    }
}
