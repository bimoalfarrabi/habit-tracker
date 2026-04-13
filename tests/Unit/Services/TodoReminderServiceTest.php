<?php

namespace Tests\Unit\Services;

use App\Mail\TodoReminderMail;
use App\Models\Todo;
use App\Models\User;
use App\Models\UserNotificationSetting;
use App\Models\UserNotification;
use App\Services\TodoReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TodoReminderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_run_does_not_create_duplicate_reminders_for_same_day(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        Todo::factory()->for($user)->create([
            'title' => 'Prepare standup notes',
            'is_completed' => false,
            'due_date' => now()->toDateString(),
            'reminder_time' => now()->format('H:i:s'),
        ]);

        $service = app(TodoReminderService::class);

        $service->run();
        $service->run();

        $this->assertSame(1, UserNotification::query()->where('type', 'todo_reminder')->count());
        Mail::assertSent(TodoReminderMail::class, 1);
    }

    public function test_run_skips_completed_todo(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        Todo::factory()->for($user)->create([
            'title' => 'Pay utility bill',
            'is_completed' => true,
            'completed_at' => now(),
            'due_date' => now()->toDateString(),
            'reminder_time' => now()->format('H:i:s'),
        ]);

        $service = app(TodoReminderService::class);
        $service->run();

        $this->assertSame(0, UserNotification::query()->where('type', 'todo_reminder')->count());
        Mail::assertNothingSent();
    }

    public function test_run_creates_in_app_notification_but_skips_email_when_email_channel_disabled(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        UserNotificationSetting::factory()->for($user)->create([
            'email_notifications_enabled' => false,
        ]);

        Todo::factory()->for($user)->create([
            'title' => 'Review weekly plan',
            'is_completed' => false,
            'due_date' => now()->toDateString(),
            'reminder_time' => now()->format('H:i:s'),
        ]);

        $service = app(TodoReminderService::class);
        $service->run();

        $this->assertSame(1, UserNotification::query()->where('type', 'todo_reminder')->count());
        Mail::assertNothingSent();
    }
}
