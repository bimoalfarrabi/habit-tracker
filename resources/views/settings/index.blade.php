@extends('layouts.app')

@section('content')
    @php
        $emailEnabled = (bool) old('email_notifications_enabled', $notificationSettings['email_notifications_enabled']);
        $telegramEnabled = (bool) old('telegram_notifications_enabled', $notificationSettings['telegram_notifications_enabled']);
        $telegramChatId = old('telegram_chat_id', $notificationSettings['telegram_chat_id']);
    @endphp

    <x-page-header title="Settings" subtitle="Atur channel pengiriman notifikasi pengingat untuk akun kamu." />

    <x-card>
        <form method="POST" action="{{ route('settings.notifications.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="rounded-soft border border-borderCream bg-sand/60 p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl text-ink">Notifikasi Email</h2>
                        <p class="mt-1 text-sm text-warmText">
                            Jika aktif, reminder habit dan todo akan dikirim ke email akun kamu.
                        </p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-ink">
                        <input type="hidden" name="email_notifications_enabled" value="0">
                        <input
                            type="checkbox"
                            name="email_notifications_enabled"
                            value="1"
                            class="h-4 w-4 rounded border-borderCream text-terracotta focus:ring-focusBlue"
                            @checked($emailEnabled)
                        >
                        Aktif
                    </label>
                </div>
            </div>

            <div class="rounded-soft border border-borderCream bg-sand/60 p-4" data-telegram-settings-root>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl text-ink">Notifikasi Telegram</h2>
                        <p class="mt-1 text-sm text-warmText">
                            Siapkan konfigurasi Telegram dari sekarang. Pengiriman Telegram akan diaktifkan pada fase fitur berikutnya.
                        </p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-ink">
                        <input type="hidden" name="telegram_notifications_enabled" value="0">
                        <input
                            type="checkbox"
                            name="telegram_notifications_enabled"
                            value="1"
                            class="h-4 w-4 rounded border-borderCream text-terracotta focus:ring-focusBlue"
                            data-telegram-toggle
                            @checked($telegramEnabled)
                        >
                        Aktif
                    </label>
                </div>

                <div class="mt-4">
                    <x-input-label for="telegram_chat_id" value="Telegram Chat ID" />
                    <x-text-input
                        id="telegram_chat_id"
                        name="telegram_chat_id"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Contoh: 123456789 atau -100123456789"
                        :value="$telegramChatId"
                        data-telegram-chat-id
                        :disabled="! $telegramEnabled"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('telegram_chat_id')" />
                    <p class="mt-2 text-xs text-mutedText">
                        Tips: chat ID bisa didapatkan dari bot helper Telegram (misalnya `@userinfobot`).
                    </p>
                </div>
            </div>

            <div class="flex justify-end">
                <x-button type="submit">Simpan Pengaturan</x-button>
            </div>
        </form>
    </x-card>
@endsection

@push('scripts')
    <script>
        (() => {
            const root = document.querySelector('[data-telegram-settings-root]');
            if (!root) {
                return;
            }

            const toggle = root.querySelector('[data-telegram-toggle]');
            const chatIdInput = root.querySelector('[data-telegram-chat-id]');

            if (!toggle || !chatIdInput) {
                return;
            }

            const applyState = () => {
                chatIdInput.disabled = !toggle.checked;
            };

            toggle.addEventListener('change', applyState);
            applyState();
        })();
    </script>
@endpush
