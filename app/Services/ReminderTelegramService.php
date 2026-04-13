<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Throwable;

class ReminderTelegramService
{
    public function sendHabitReminder(User $user, Habit $habit, Carbon $scheduledFor): void
    {
        $chatId = $this->getEligibleChatId($user);
        if (! $chatId) {
            return;
        }

        $message = implode("\n", [
            'Ritme Reminder',
            "Habit: {$habit->title}",
            "Waktu: {$scheduledFor->copy()->format('d M Y, H:i T')}",
            'Jangan lupa check-in hari ini.',
        ]);

        $this->sendMessage($chatId, $message);
    }

    public function sendTodoReminder(User $user, Todo $todo, Carbon $scheduledFor): void
    {
        $chatId = $this->getEligibleChatId($user);
        if (! $chatId) {
            return;
        }

        $message = implode("\n", [
            'Ritme Reminder',
            "Todo: {$todo->title}",
            "Waktu: {$scheduledFor->copy()->format('d M Y, H:i T')}",
            'Yuk selesaikan sebelum terlewat.',
        ]);

        $this->sendMessage($chatId, $message);
    }

    private function getEligibleChatId(User $user): ?string
    {
        $botToken = (string) config('services.telegram.bot_token');

        if (trim($botToken) === '') {
            return null;
        }

        $settings = $user->notificationSettings;
        if ($settings === null || ! $settings->telegram_notifications_enabled) {
            return null;
        }

        $chatId = trim((string) $settings->telegram_chat_id);

        return $chatId !== '' ? $chatId : null;
    }

    private function sendMessage(string $chatId, string $message): void
    {
        $botToken = (string) config('services.telegram.bot_token');
        $baseUrl = rtrim((string) config('services.telegram.base_url', 'https://api.telegram.org'), '/');

        try {
            $response = Http::timeout(10)->asForm()->post(
                "{$baseUrl}/bot{$botToken}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]
            );

            if (! $response->successful()) {
                report(new \RuntimeException('Telegram reminder send failed with status '.$response->status()));
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
