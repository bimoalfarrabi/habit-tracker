<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_all_as_read_updates_unread_notifications(): void
    {
        $user = User::factory()->create();

        UserNotification::factory()->for($user)->count(3)->create([
            'is_read' => false,
            'read_at' => null,
        ]);

        $service = app(NotificationService::class);
        $updatedCount = $service->markAllAsRead($user);

        $this->assertSame(3, $updatedCount);
        $this->assertSame(0, UserNotification::query()->where('user_id', $user->id)->where('is_read', false)->count());
    }
}
