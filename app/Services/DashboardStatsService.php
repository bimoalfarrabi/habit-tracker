<?php

namespace App\Services;

use App\Models\FocusSession;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardStatsService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected StreakService $streakService
    ) {}

    public function getForUser(User $user): array
    {
        $today = now()->toDateString();

        $totalActiveHabits = Habit::query()
            ->where('user_id', $user->id)
            ->active()
            ->count();

        $completedToday = HabitLog::query()
            ->where('user_id', $user->id)
            ->whereDate('log_date', $today)
            ->where('status', 'completed')
            ->count();

        $focusSecondsToday = FocusSession::query()
            ->where('user_id', $user->id)
            ->whereDate('session_date', $today)
            ->sum('focused_duration_seconds');

        $pendingTodos = Todo::query()
            ->where('user_id', $user->id)
            ->pending()
            ->count();

        $dueTodayTodos = Todo::query()
            ->where('user_id', $user->id)
            ->pending()
            ->whereDate('due_date', $today)
            ->count();

        return [
            'total_active_habits' => $totalActiveHabits,
            'completed_today' => $completedToday,
            'current_streak' => $this->streakService->getCurrentStreakForUser($user),
            'focus_minutes_today' => (int) floor($focusSecondsToday / 60),
            'unread_notifications' => $this->notificationService->getUnreadCount($user),
            'pending_todos' => $pendingTodos,
            'due_today_todos' => $dueTodayTodos,
        ];
    }

    public function getTodayHabitsForUser(User $user): Collection
    {
        $today = now()->toDateString();

        $habits = Habit::query()
            ->where('user_id', $user->id)
            ->active()
            ->with(['logs' => function ($query) use ($today): void {
                $query->whereDate('log_date', $today);
            }])
            ->orderBy('reminder_time')
            ->latest('id')
            ->get()
            ->map(function (Habit $habit) {
                $todayLog = $habit->logs->first();

                return [
                    'habit' => $habit,
                    'is_completed_today' => (bool) ($todayLog && $todayLog->status === 'completed'),
                    'today_log' => $todayLog,
                    'current_streak_for_habit' => $this->streakService->getCurrentStreakForHabit($habit),
                ];
            })
            ->sortBy(fn (array $item) => $item['is_completed_today'])
            ->values();

        return $habits;
    }

    public function getWeeklyCompletionSeries(User $user): array
    {
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $rows = HabitLog::query()
            ->selectRaw('log_date, COUNT(*) as completed_count')
            ->where('user_id', $user->id)
            ->whereBetween('log_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'completed')
            ->groupBy('log_date')
            ->pluck('completed_count', 'log_date');

        $series = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->toDateString();
            $series[] = [
                'date' => $dateKey,
                'completed_count' => (int) ($rows[$dateKey] ?? 0),
            ];
        }

        return $series;
    }

    public function getPendingTodosForUser(User $user, int $limit = 5): Collection
    {
        return Todo::query()
            ->where('user_id', $user->id)
            ->pending()
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->latest('id')
            ->take($limit)
            ->get();
    }
}
