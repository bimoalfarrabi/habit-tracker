<?php

namespace App\Services;

use App\Models\FocusSession;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FocusSessionService
{
    public function start(User $user, array $data): array
    {
        $existingRunning = FocusSession::query()
            ->where('user_id', $user->id)
            ->where('status', 'running')
            ->latest('id')
            ->first();

        if ($existingRunning) {
            return [
                'session' => $existingRunning,
                'reused_existing_session' => true,
            ];
        }

        $habitId = $data['habit_id'] ?? null;

        if ($habitId) {
            $isOwned = Habit::query()
                ->where('id', $habitId)
                ->where('user_id', $user->id)
                ->exists();

            if (! $isOwned) {
                throw new AuthorizationException('You are not allowed to access this resource.');
            }
        }

        $session = FocusSession::query()->create([
            'user_id' => $user->id,
            'habit_id' => $habitId,
            'session_date' => now()->toDateString(),
            'start_time' => now(),
            'planned_duration_minutes' => $data['planned_duration_minutes'] ?? null,
            'total_duration_seconds' => 0,
            'focused_duration_seconds' => 0,
            'unfocused_duration_seconds' => 0,
            'interruption_count' => 0,
            'status' => 'running',
            'note' => $data['note'] ?? null,
        ]);

        return [
            'session' => $session,
            'reused_existing_session' => false,
        ];
    }

    public function stop(User $user, FocusSession $session, array $data): FocusSession
    {
        if ($session->user_id !== $user->id) {
            throw new AuthorizationException('You are not allowed to access this resource.');
        }

        if ($session->status !== 'running') {
            throw new InvalidArgumentException('Sesi fokus sudah tidak aktif.');
        }

        $focused = (int) ($data['focused_duration_seconds'] ?? 0);
        $unfocused = (int) ($data['unfocused_duration_seconds'] ?? 0);
        $interruptionCount = (int) ($data['interruption_count'] ?? 0);

        return DB::transaction(function () use ($session, $focused, $unfocused, $interruptionCount, $data) {
            $session->update([
                'end_time' => now(),
                'focused_duration_seconds' => max(0, $focused),
                'unfocused_duration_seconds' => max(0, $unfocused),
                'total_duration_seconds' => max(0, $focused) + max(0, $unfocused),
                'interruption_count' => max(0, $interruptionCount),
                'status' => $data['status'],
            ]);

            return $session->refresh();
        });
    }

    public function getTodaySummary(User $user): array
    {
        $query = FocusSession::query()
            ->where('user_id', $user->id)
            ->whereDate('session_date', now()->toDateString())
            ->whereIn('status', ['completed', 'cancelled']);

        $totalFocusSeconds = (clone $query)->sum('focused_duration_seconds');
        $totalBackgroundSeconds = (clone $query)->sum('unfocused_duration_seconds');

        return [
            'total_sessions' => (clone $query)->count(),
            'focus_minutes_today' => (int) floor($totalFocusSeconds / 60),
            'background_minutes_today' => (int) floor($totalBackgroundSeconds / 60),
            'interruption_count_today' => (int) (clone $query)->sum('interruption_count'),
        ];
    }
}
