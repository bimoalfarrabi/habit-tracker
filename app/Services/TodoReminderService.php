<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\UserNotification;
use Carbon\Carbon;

class TodoReminderService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected ReminderEmailService $reminderEmailService
    ) {}

    public function run(): array
    {
        $processed = 0;
        $created = 0;
        $skipped = 0;

        Todo::query()
            ->pending()
            ->whereDate('due_date', now()->toDateString())
            ->whereNotNull('reminder_time')
            ->with('user')
            ->chunkById(100, function ($todos) use (&$processed, &$created, &$skipped): void {
                $now = now();

                foreach ($todos as $todo) {
                    $processed++;

                    if ($this->processTodo($todo, $now)) {
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

    public function processTodo(Todo $todo, Carbon $now): bool
    {
        if (! $this->shouldSendReminder($todo, $now)) {
            return false;
        }

        $alreadyNotified = UserNotification::query()
            ->where('user_id', $todo->user_id)
            ->where('type', 'todo_reminder')
            ->whereDate('created_at', $now->toDateString())
            ->where('data->todo_id', $todo->id)
            ->exists();

        if ($alreadyNotified) {
            return false;
        }

        $this->notificationService->createForUser($todo->user, [
            'type' => 'todo_reminder',
            'title' => 'Reminder Todo',
            'message' => "Jangan lupa todo: {$todo->title}",
            'data' => [
                'todo_id' => $todo->id,
                'todo_title' => $todo->title,
                'due_date' => $todo->due_date?->toDateString(),
            ],
            'scheduled_for' => $now,
        ]);

        $this->reminderEmailService->sendTodoReminder($todo->user, $todo, $now);

        return true;
    }

    public function shouldSendReminder(Todo $todo, Carbon $now): bool
    {
        if ($todo->is_completed || ! $todo->due_date || ! $todo->reminder_time) {
            return false;
        }

        if ($todo->due_date->toDateString() !== $now->toDateString()) {
            return false;
        }

        $rawReminderTime = (string) $todo->reminder_time;
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
