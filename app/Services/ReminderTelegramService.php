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
        $credentials = $this->resolveCredentials($user);
        if ($credentials === null) {
            return;
        }

        $message = implode("\n", [
            'Ritme Reminder',
            "Habit: {$habit->title}",
            "Waktu: {$scheduledFor->copy()->format('d M Y, H:i T')}",
            'Jangan lupa check-in hari ini.',
        ]);

        $this->sendMessage($credentials['bot_token'], $credentials['chat_id'], $message);
    }

    public function sendTodoReminder(User $user, Todo $todo, Carbon $scheduledFor): void
    {
        $credentials = $this->resolveCredentials($user);
        if ($credentials === null) {
            return;
        }

        $message = implode("\n", [
            'Ritme Reminder',
            "Todo: {$todo->title}",
            "Waktu: {$scheduledFor->copy()->format('d M Y, H:i T')}",
            'Yuk selesaikan sebelum terlewat.',
        ]);

        $this->sendMessage($credentials['bot_token'], $credentials['chat_id'], $message);
    }

    /**
     * @return array{chat_id:string,bot_token:string}|null
     */
    private function resolveCredentials(User $user): ?array
    {
        $settings = $user->notificationSettings;
        if ($settings === null || ! $settings->telegram_notifications_enabled) {
            return null;
        }

        $chatId = trim((string) $settings->telegram_chat_id);
        if ($chatId === '') {
            return null;
        }

        $botToken = trim((string) $settings->telegram_bot_token);
        if ($botToken === '') {
            $botToken = trim((string) config('services.telegram.bot_token'));
        }

        if ($botToken === '') {
            return null;
        }

        return [
            'chat_id' => $chatId,
            'bot_token' => $botToken,
        ];
    }

    private function sendMessage(string $botToken, string $chatId, string $message): void
    {
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
