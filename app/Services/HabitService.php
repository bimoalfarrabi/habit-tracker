<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class HabitService
{
    public function createForUser(User $user, array $data): Habit
    {
        return Habit::query()->create([
            ...$data,
            'user_id' => $user->id,
            'is_active' => $data['is_active'] ?? true,
            'target_count' => $data['target_count'] ?? 1,
        ]);
    }

    public function updateForUser(User $user, Habit $habit, array $data): Habit
    {
        $this->ensureOwned($user, $habit);

        $habit->update($data);

        return $habit->refresh();
    }

    public function archiveForUser(User $user, Habit $habit): Habit
    {
        $this->ensureOwned($user, $habit);

        $habit->update([
            'is_active' => false,
            'archived_at' => now(),
        ]);

        return $habit->refresh();
    }

    public function toggleActiveForUser(User $user, Habit $habit): Habit
    {
        $this->ensureOwned($user, $habit);

        $nextState = ! $habit->is_active;

        $habit->update([
            'is_active' => $nextState,
            'archived_at' => $nextState ? null : now(),
        ]);

        return $habit->refresh();
    }

    private function ensureOwned(User $user, Habit $habit): void
    {
        if ($habit->user_id !== $user->id) {
            throw new AuthorizationException('You are not allowed to access this resource.');
        }
    }
}
