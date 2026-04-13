<?php

namespace App\Services;

use App\Mail\HabitReminderMail;
use App\Mail\TodoReminderMail;
use App\Models\Habit;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ReminderEmailService
{
    public function sendHabitReminder(User $user, Habit $habit, Carbon $scheduledFor): void
    {
        if (! $user->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new HabitReminderMail(
                user: $user,
                habit: $habit,
                scheduledAtLabel: $scheduledFor->copy()->format('d M Y, H:i T'),
            ));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function sendTodoReminder(User $user, Todo $todo, Carbon $scheduledFor): void
    {
        if (! $user->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new TodoReminderMail(
                user: $user,
                todo: $todo,
                scheduledAtLabel: $scheduledFor->copy()->format('d M Y, H:i T'),
            ));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
