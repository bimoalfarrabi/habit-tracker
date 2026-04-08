<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\UserNotification;
use Carbon\Carbon;

class HabitReminderService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function run(): array
    {
        $processed = 0;
        $created = 0;
        $skipped = 0;

        Habit::query()
            ->active()
            ->whereNotNull('reminder_time')
            ->with('user')
            ->chunkById(100, function ($habits) use (&$processed, &$created, &$skipped): void {
                $now = now();

                foreach ($habits as $habit) {
                    $processed++;

                    if ($this->processHabit($habit, $now)) {
                        $created++;
                    } else {
                        $skipped++;
                    }
                }
            });

        return [
            'processed' => $processed,
            'created' => $created,
            'skipped' => $skipped,
        ];
    }

    public function processHabit(Habit $habit, Carbon $now): bool
    {
        if (! $this->shouldSendReminder($habit, $now)) {
            return false;
        }

        $alreadyCompleted = HabitLog::query()
            ->where('habit_id', $habit->id)
            ->where('user_id', $habit->user_id)
            ->whereDate('log_date', $now->toDateString())
            ->where('status', 'completed')
            ->exists();

        if ($alreadyCompleted) {
            return false;
        }

        $alreadyNotified = UserNotification::query()
            ->where('user_id', $habit->user_id)
            ->where('type', 'habit_reminder')
            ->whereDate('created_at', $now->toDateString())
            ->where('data->habit_id', $habit->id)
            ->exists();

        if ($alreadyNotified) {
            return false;
        }

        $this->notificationService->createForUser($habit->user, [
            'type' => 'habit_reminder',
            'title' => 'Reminder Habit',
            'message' => "Jangan lupa: {$habit->title}",
            'data' => [
                'habit_id' => $habit->id,
                'habit_title' => $habit->title,
            ],
            'scheduled_for' => $now,
        ]);

        return true;
    }

    public function shouldSendReminder(Habit $habit, Carbon $now): bool
    {
        if (! $habit->is_active || $habit->archived_at !== null || ! $habit->reminder_time) {
            return false;
        }

        $rawReminderTime = (string) $habit->reminder_time;
        $format = str_contains($rawReminderTime, ':') && substr_count($rawReminderTime, ':') === 2
            ? 'H:i:s'
            : 'H:i';

        try {
            $target = Carbon::createFromFormat($format, $rawReminderTime, $now->getTimezone());
        } catch (\Throwable) {
            return false;
        }

        $current = Carbon::createFromFormat('H:i', $now->format('H:i'), $now->getTimezone());

        return abs($target->diffInMinutes($current, false)) <= 1;
    }
}
