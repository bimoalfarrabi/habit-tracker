<?php

namespace Tests\Unit\Services;

use App\Models\Habit;
use App\Models\Todo;
use App\Models\User;
use App\Models\UserNotificationSetting;
use App\Services\ReminderTelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReminderTelegramServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_habit_reminder_sends_message_when_user_is_eligible(): void
    {
        config()->set('services.telegram.bot_token', null);
        config()->set('services.telegram.base_url', 'https://api.telegram.org');

        Http::fake([
            '*' => Http::response(['ok' => true], 200),
        ]);

        $user = User::factory()->create();
        UserNotificationSetting::factory()->for($user)->create([
            'telegram_notifications_enabled' => true,
            'telegram_chat_id' => '-100987654321',
            'telegram_bot_token' => 'user-token-123',
        ]);

        $habit = Habit::factory()->for($user)->create([
            'title' => 'Morning Run',
        ]);

        app(ReminderTelegramService::class)->sendHabitReminder($user->refresh(), $habit, now());

        Http::assertSent(function ($request): bool {
            $payload = $request->data();

            return $request->url() === 'https://api.telegram.org/botuser-token-123/sendMessage'
                && ($payload['chat_id'] ?? null) === '-100987654321'
                && str_contains((string) ($payload['text'] ?? ''), 'Morning Run');
        });
    }

    public function test_send_todo_reminder_skips_when_user_and_global_bot_token_are_missing(): void
    {
        config()->set('services.telegram.bot_token', null);

        Http::fake();

        $user = User::factory()->create();
        UserNotificationSetting::factory()->for($user)->create([
            'telegram_notifications_enabled' => true,
            'telegram_chat_id' => '123456789',
            'telegram_bot_token' => null,
        ]);

        $todo = Todo::factory()->for($user)->create([
            'title' => 'Finish report',
        ]);

        app(ReminderTelegramService::class)->sendTodoReminder($user->refresh(), $todo, now());

        Http::assertNothingSent();
    }

    public function test_send_todo_reminder_uses_global_fallback_token_when_user_token_is_missing(): void
    {
        config()->set('services.telegram.bot_token', 'global-token-xyz');
        config()->set('services.telegram.base_url', 'https://api.telegram.org');

        Http::fake([
            '*' => Http::response(['ok' => true], 200),
        ]);

        $user = User::factory()->create();
        UserNotificationSetting::factory()->for($user)->create([
            'telegram_notifications_enabled' => true,
            'telegram_chat_id' => '123456789',
            'telegram_bot_token' => null,
        ]);

        $todo = Todo::factory()->for($user)->create([
            'title' => 'Finish report',
        ]);

        app(ReminderTelegramService::class)->sendTodoReminder($user->refresh(), $todo, now());

        Http::assertSent(function ($request): bool {
            $payload = $request->data();

            return $request->url() === 'https://api.telegram.org/botglobal-token-xyz/sendMessage'
                && ($payload['chat_id'] ?? null) === '123456789'
                && str_contains((string) ($payload['text'] ?? ''), 'Finish report');
        });
    }

    public function test_send_todo_reminder_skips_when_telegram_channel_disabled(): void
    {
        config()->set('services.telegram.bot_token', 'dummy-token');

        Http::fake();

        $user = User::factory()->create();
        UserNotificationSetting::factory()->for($user)->create([
            'telegram_notifications_enabled' => false,
            'telegram_chat_id' => '123456789',
            'telegram_bot_token' => 'user-token-abc',
        ]);

        $todo = Todo::factory()->for($user)->create([
            'title' => 'Read 10 pages',
        ]);

        app(ReminderTelegramService::class)->sendTodoReminder($user->refresh(), $todo, now());

        Http::assertNothingSent();
    }
}
