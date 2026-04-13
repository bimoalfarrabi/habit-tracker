<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_requires_authentication(): void
    {
        $response = $this->get(route('settings.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_notification_settings_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings.index'));

        $response
            ->assertOk()
            ->assertSee('Notifikasi Email')
            ->assertSee('Notifikasi Telegram');
    }

    public function test_user_can_update_notification_channel_preferences(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('settings.notifications.update'), [
                'email_notifications_enabled' => '0',
                'telegram_notifications_enabled' => '1',
                'telegram_chat_id' => '-1001234567890',
                'telegram_bot_token' => '123456789:token-abc',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('settings.index'));

        $settings = $user->refresh()->notificationSettings;

        $this->assertNotNull($settings);
        $this->assertFalse($settings->email_notifications_enabled);
        $this->assertTrue($settings->telegram_notifications_enabled);
        $this->assertSame('-1001234567890', $settings->telegram_chat_id);
        $this->assertSame('123456789:token-abc', $settings->telegram_bot_token);
    }

    public function test_telegram_chat_id_is_required_when_telegram_notifications_enabled(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('settings.index'))
            ->patch(route('settings.notifications.update'), [
                'email_notifications_enabled' => '1',
                'telegram_notifications_enabled' => '1',
                'telegram_chat_id' => '',
            ]);

        $response
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasErrors('telegram_chat_id');
    }

    public function test_telegram_bot_token_is_required_when_enabling_and_user_has_no_saved_token(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('settings.index'))
            ->patch(route('settings.notifications.update'), [
                'email_notifications_enabled' => '1',
                'telegram_notifications_enabled' => '1',
                'telegram_chat_id' => '123456789',
                'telegram_bot_token' => '',
            ]);

        $response
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasErrors('telegram_bot_token');
    }
}
