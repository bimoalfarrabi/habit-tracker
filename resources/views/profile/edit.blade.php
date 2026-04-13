@extends('layouts.app')

@section('content')
    @php
        $cooldownSeconds = (int) ($verificationCooldownSeconds ?? session('verification_cooldown_seconds', 0));
    @endphp

    <x-page-header title="Profile" subtitle="Kelola informasi akun dan keamanan login kamu.">
        <x-slot:actions>
            <div class="flex items-center gap-2" data-verification-cooldown-root>
                <span class="badge-soft">
                    {{ $user->hasVerifiedEmail() ? 'Email terverifikasi' : 'Email belum terverifikasi' }}
                </span>
                @if (! $user->hasVerifiedEmail())
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-button
                            type="submit"
                            variant="secondary"
                            data-verification-resend-button
                            data-cooldown-seconds="{{ $cooldownSeconds }}"
                            data-default-label="Kirim Ulang Verifikasi"
                            data-countdown-label="Tunggu :seconds detik"
                            :disabled="$cooldownSeconds > 0"
                        >
                            {{ $cooldownSeconds > 0 ? "Tunggu {$cooldownSeconds} detik" : 'Kirim Ulang Verifikasi' }}
                        </x-button>
                    </form>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    @if (! $user->hasVerifiedEmail() && session('status') === 'verification-link-sent')
        <x-card class="mb-6 border-emerald-200 bg-[#ecfdf3]">
            <p class="text-sm font-semibold text-emerald-700">
                Link verifikasi baru sudah dikirim ke email kamu.
            </p>
        </x-card>
    @endif

    @if (! $user->hasVerifiedEmail() && $cooldownSeconds > 0)
        <x-card class="mb-6 border-amber-200 bg-[#fff6eb]" data-verification-cooldown-root data-verification-cooldown-message>
            <p class="text-sm font-semibold text-amber-700">
                Cooldown aktif: kirim ulang tersedia lagi dalam <span data-verification-cooldown-seconds>{{ $cooldownSeconds }}</span> detik.
            </p>
        </x-card>
    @endif

    @php
        $activeTab = request('tab', 'settings');
        if (! in_array($activeTab, ['settings', 'activity'], true)) {
            $activeTab = 'settings';
        }

        $nameParts = preg_split('/\s+/', trim($user->name));
        $initials = collect($nameParts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    @endphp

    <x-card class="mb-6">
        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-terracotta text-xl font-semibold text-ivory shadow-ringWarm">
                    @if ($user->profile_photo_url)
                        <img
                            src="{{ $user->profile_photo_url }}"
                            alt="Foto {{ $user->name }}"
                            class="h-16 w-16 rounded-full object-cover"
                        >
                    @else
                        {{ $initials ?: 'U' }}
                    @endif
                </div>
                <div>
                    <p class="text-2xl text-ink">{{ $user->name }}</p>
                    <p class="text-sm text-warmText">{{ $user->email }}</p>
                    <p class="mt-1 text-xs text-mutedText">Member sejak {{ $user->created_at?->format('d M Y') }}</p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <x-metric-card label="Total Habits" :value="$profileStats['total_habits']" />
                <x-metric-card label="Active Habits" :value="$profileStats['active_habits']" />
                <x-metric-card label="Completed Today" :value="$profileStats['completed_today']" />
                <x-metric-card label="Focus Minutes" :value="$profileStats['focus_minutes']" />
                <x-metric-card label="Total Sessions" :value="$profileStats['total_focus_sessions']" />
            </div>
        </div>
    </x-card>

    <div class="mb-5 flex items-center gap-2">
        <a href="{{ route('profile.edit', ['tab' => 'settings']) }}" class="{{ $activeTab === 'settings' ? 'btn-primary-warm' : 'btn-secondary-warm' }}">
            Profile Settings
        </a>
        <a href="{{ route('profile.edit', ['tab' => 'activity']) }}" class="{{ $activeTab === 'activity' ? 'btn-primary-warm' : 'btn-secondary-warm' }}">
            Activity
        </a>
    </div>

    @if ($activeTab === 'settings')
        <div class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
            <x-card>
                @include('profile.partials.update-profile-information-form')
            </x-card>

            <div class="space-y-6">
                <x-card>
                    @include('profile.partials.update-password-form')
                </x-card>

                <x-card class="border-dangerWarm/30">
                    @include('profile.partials.delete-user-form')
                </x-card>
            </div>
        </div>
    @else
        <x-card>
            <h2 class="text-2xl text-ink">Recent Activity</h2>
            <p class="mt-1 text-sm text-warmText">Riwayat aktivitas terbaru dari habit, focus session, dan notifikasi.</p>

            <div class="mt-4 space-y-3">
                @forelse ($recentActivities as $activity)
                    <div class="rounded-soft border border-borderCream bg-ivory p-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-mutedText">{{ $activity['type'] }}</p>
                            <p class="text-xs text-mutedText">{{ $activity['occurred_at']->diffForHumans() }}</p>
                        </div>
                        <p class="mt-2 text-sm font-semibold text-ink">{{ $activity['title'] }}</p>
                        <p class="mt-1 text-sm text-warmText">{{ $activity['description'] }}</p>
                    </div>
                @empty
                    <p class="rounded-soft border border-borderCream bg-sand p-4 text-sm text-warmText">
                        Belum ada aktivitas yang bisa ditampilkan.
                    </p>
                @endforelse
            </div>
        </x-card>
    @endif
@endsection
