<x-guest-layout>
    @php
        $cooldownSeconds = (int) ($verificationCooldownSeconds ?? session('verification_cooldown_seconds', 0));
    @endphp

    <div data-verification-cooldown-root>
        <div class="mb-4 text-sm text-gray-600">
            Silakan verifikasi alamat email kamu dulu sebelum mulai menggunakan aplikasi.
            Klik link verifikasi yang kami kirim ke inbox kamu. Jika belum menerima emailnya, kirim ulang lewat tombol di bawah.
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                Link verifikasi baru sudah dikirim ke alamat email kamu.
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <div data-verification-cooldown-message class="mb-4 font-medium text-sm text-amber-700 {{ $cooldownSeconds > 0 ? '' : 'hidden' }}">
            Kamu bisa kirim ulang lagi dalam <span data-verification-cooldown-seconds>{{ $cooldownSeconds }}</span> detik.
        </div>

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-primary-button
                        data-verification-resend-button
                        data-cooldown-seconds="{{ $cooldownSeconds }}"
                        data-default-label="Kirim Ulang Email Verifikasi"
                        data-countdown-label="Tunggu :seconds detik"
                        :disabled="$cooldownSeconds > 0"
                    >
                        {{ $cooldownSeconds > 0 ? "Tunggu {$cooldownSeconds} detik" : 'Kirim Ulang Email Verifikasi' }}
                    </x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
