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
            ],
        ]);
    }

    public function updateNotifications(UpdateNotificationChannelSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if (! $data['telegram_notifications_enabled']) {
            $data['telegram_chat_id'] = null;
        }

        $user->notificationSettings()->updateOrCreate([], $data);

        return redirect()
            ->route('settings.index')
            ->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }
}
