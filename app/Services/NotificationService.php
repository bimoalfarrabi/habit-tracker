<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function createForUser(User $user, array $payload): UserNotification
    {
        return UserNotification::query()->create([
            'user_id' => $user->id,
            'type' => $payload['type'],
            'title' => $payload['title'],
            'message' => $payload['message'],
            'data' => $payload['data'] ?? null,
            'scheduled_for' => $payload['scheduled_for'] ?? null,
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function markAsRead(UserNotification $notification): UserNotification
    {
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $notification->refresh();
    }

    public function markAllAsRead(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function getLatestForUser(User $user, int $limit = 10): Collection
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->take($limit)
            ->get();
    }

    public function getUnreadCount(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }
}
