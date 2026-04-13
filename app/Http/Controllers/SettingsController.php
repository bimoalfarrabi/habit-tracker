<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateNotificationChannelSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = request()->user()->notificationSettings;

        return view('settings.index', [
            'notificationSettings' => [
                'email_notifications_enabled' => $settings?->email_notifications_enabled ?? true,
                'telegram_notifications_enabled' => $settings?->telegram_notifications_enabled ?? false,
                'telegram_chat_id' => $settings?->telegram_chat_id,
                'has_telegram_bot_token' => filled($settings?->telegram_bot_token),
            ],
            'telegramFallbackBotConfigured' => filled(config('services.telegram.bot_token')),
        ]);
    }

    public function updateNotifications(UpdateNotificationChannelSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $settings = $user->notificationSettings()->firstOrNew();

        $settings->email_notifications_enabled = (bool) $data['email_notifications_enabled'];
        $settings->telegram_notifications_enabled = (bool) $data['telegram_notifications_enabled'];

        if (array_key_exists('telegram_chat_id', $data)) {
            $settings->telegram_chat_id = $data['telegram_chat_id'];
        }

        $telegramBotToken = trim((string) ($data['telegram_bot_token'] ?? ''));
        if ($telegramBotToken !== '') {
            $settings->telegram_bot_token = $telegramBotToken;
        }

        $settings->save();

        return redirect()
            ->route('settings.index')
            ->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }
}
