<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Carbon\Carbon;

class StreakService
{
    public function getCurrentStreakForHabit(Habit $habit): int
    {
        $dates = $habit->logs()
            ->where('status', 'completed')
            ->pluck('log_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->values()
            ->toArray();

        return $this->calculateCurrentStreak($dates);
    }

    public function getLongestStreakForHabit(Habit $habit): int
    {
        $dates = $habit->logs()
            ->where('status', 'completed')
            ->pluck('log_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->values()
            ->toArray();

        return $this->calculateLongestStreak($dates);
    }

    public function getCurrentStreakForUser(User $user): int
    {
        $dates = HabitLog::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->pluck('log_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->values()
            ->toArray();

        return $this->calculateCurrentStreak($dates);
    }

    public function getHabitCompletionRate(Habit $habit, int $days = 7): float
    {
        if ($days < 1) {
            return 0.0;
        }

        $endDate = now()->toDateString();
        $startDate = now()->subDays($days - 1)->toDateString();

        $completedDays = $habit->logs()
            ->whereBetween('log_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        return round(($completedDays / $days) * 100, 2);
    }

    private function calculateCurrentStreak(array $dates): int
    {
        if ($dates === []) {
            return 0;
        }

        $dateSet = array_fill_keys($dates, true);
        $today = now()->toDateString();
        $start = isset($dateSet[$today]) ? Carbon::parse($today) : Carbon::yesterday();

        if (! isset($dateSet[$start->toDateString()])) {
            return 0;
        }

        $streak = 0;

        while (isset($dateSet[$start->toDateString()])) {
            $streak++;
            $start->subDay();
        }

        return $streak;
    }

    private function calculateLongestStreak(array $dates): int
    {
        if ($dates === []) {
            return 0;
        }

        usort($dates, fn (string $a, string $b) => strcmp($a, $b));

        $longest = 1;
        $current = 1;

        for ($index = 1, $count = count($dates); $index < $count; $index++) {
            $previous = Carbon::parse($dates[$index - 1]);
            $currentDate = Carbon::parse($dates[$index]);

            if ($previous->copy()->addDay()->isSameDay($currentDate)) {
                $current++;
            } else {
                $current = 1;
            }

            $longest = max($longest, $current);
        }

        return $longest;
    }
}
