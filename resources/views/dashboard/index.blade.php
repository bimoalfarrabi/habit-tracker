@extends('layouts.app')

@section('content')
    @php
        $isEmailVerified = (bool) auth()->user()?->hasVerifiedEmail();
        $cooldownSeconds = (int) ($verificationCooldownSeconds ?? session('verification_cooldown_seconds', 0));
    @endphp

    <x-page-header title="Dashboard" subtitle="Ringkasan kecil untuk menjaga ritme hari ini.">
        <x-slot:actions>
            @if ($isEmailVerified)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('habits.create') }}" class="btn-primary-warm">+ Tambah Habit</a>
                    <a href="{{ route('todos.create') }}" class="btn-secondary-warm">+ Tambah Todo</a>
                </div>
            @else
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-button type="submit" variant="secondary" :disabled="$cooldownSeconds > 0">
                        {{ $cooldownSeconds > 0 ? "Tunggu {$cooldownSeconds} detik" : 'Kirim Ulang Verifikasi' }}
                    </x-button>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if (! $isEmailVerified)
        <x-card class="border-amber-200 bg-[#fff6eb]">
            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#9c5a1b]">Verifikasi Email Diperlukan</p>
            <h2 class="mt-2 text-3xl text-ink">Verifikasi email untuk mengaktifkan semua fitur Ritme</h2>
            <p class="mt-2 text-sm text-warmText">
                Akun kamu belum terverifikasi. Setelah verifikasi selesai, kamu bisa akses habit, todo, focus session, notifikasi, dan fitur dashboard lengkap.
            </p>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-button type="submit" :disabled="$cooldownSeconds > 0">
                        {{ $cooldownSeconds > 0 ? "Tunggu {$cooldownSeconds} detik" : 'Kirim Ulang Email Verifikasi' }}
                    </x-button>
                </form>
                <a href="{{ route('profile.edit') }}" class="btn-secondary-warm">Perbarui Email di Profil</a>
            </div>

            @if (session('status') === 'verification-link-sent')
                <p class="mt-3 text-sm font-semibold text-emerald-700">
                    Link verifikasi baru sudah dikirim. Cek inbox/spam email kamu.
                </p>
            @endif

            @if ($cooldownSeconds > 0)
                <p class="mt-2 text-sm font-semibold text-amber-700">
                    Cooldown aktif: kirim ulang tersedia lagi dalam {{ $cooldownSeconds }} detik.
                </p>
            @endif
        </x-card>
    @else
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
            <x-metric-card label="Total Active" :value="$stats['total_active_habits']" />
            <x-metric-card label="Completed Today" :value="$stats['completed_today']" />
            <x-metric-card label="Current Streak" :value="$stats['current_streak']" hint="Hari beruntun" />
            <x-metric-card label="Focus Minutes" :value="$stats['focus_minutes_today']" />
            <x-metric-card label="Unread Notifications" :value="$stats['unread_notifications']" />
            <x-metric-card label="Pending Todos" :value="$stats['pending_todos']" hint="Due today: {{ $stats['due_today_todos'] }}" />
        </section>

        <section class="mt-6 grid gap-6 lg:grid-cols-[1.6fr_1fr]">
            <div class="space-y-4">
                <h2 class="text-3xl text-ink">Today Habits</h2>

                @forelse ($todayHabits as $item)
                    <x-habit-card
                        :habit="$item['habit']"
                        :today-log="$item['today_log']"
                        :is-completed-today="$item['is_completed_today']"
                    />
                @empty
                    <x-empty-state
                        title="Belum ada habit aktif"
                        description="Mulai dari satu kebiasaan kecil dulu, lalu jaga ritmenya tiap hari."
                    >
                        <x-slot:action>
                            <a href="{{ route('habits.create') }}" class="btn-primary-warm">Buat Habit Pertama</a>
                        </x-slot:action>
                    </x-empty-state>
                @endforelse
            </div>

            <div class="space-y-4">
                <x-card variant="dark">
                    <p class="text-sm uppercase tracking-[0.15em] text-[#c9c4bb]">Focus Session</p>
                    <p class="mt-2 text-4xl font-semibold">{{ $runningSession ? 'Running' : 'Idle' }}</p>
                    <p class="mt-2 text-sm text-[#c9c4bb]">
                        {{ $runningSession ? 'Sesi fokus sedang berjalan. Kamu bisa lanjut dari halaman Focus.' : 'Belum ada sesi fokus aktif saat ini.' }}
                    </p>
                    <a href="{{ route('focus-sessions.index') }}" class="btn-secondary-warm mt-5">Open Focus Page</a>
                </x-card>

                <x-card>
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl text-ink">Pending Todos</h3>
                        <a href="{{ route('todos.index') }}" class="btn-ghost-warm">Lihat semua</a>
                    </div>

                    <ul class="mt-4 space-y-2">
                        @forelse ($pendingTodos as $todo)
                            <li class="rounded-soft border border-borderCream bg-ivory p-3">
                                <p class="text-sm font-semibold text-ink">{{ $todo->title }}</p>
                                <p class="mt-1 text-xs text-warmText">
                                    @if ($todo->due_date)
                                        Due {{ $todo->due_date->format('d M Y') }}
                                    @else
                                        Tanpa due date
                                    @endif
                                    @if ($todo->reminder_time)
                                        • Reminder {{ \Carbon\Carbon::parse($todo->reminder_time)->format('H:i') }}
                                    @endif
                                </p>
                            </li>
                        @empty
                            <li class="text-sm text-mutedText">Belum ada todo pending.</li>
                        @endforelse
                    </ul>
                </x-card>

                <x-card>
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl text-ink">Notifications Preview</h3>
                        <a href="{{ route('notifications.index') }}" class="btn-ghost-warm">Lihat semua</a>
                    </div>

                    <ul class="mt-4 space-y-2" data-notification-preview-list>
                        @forelse ($latestNotifications as $notification)
                            <li class="rounded-soft border border-borderCream bg-ivory p-3">
                                <p class="text-sm font-semibold text-ink">{{ $notification->title }}</p>
                                <p class="mt-1 text-xs text-warmText">{{ $notification->message }}</p>
                            </li>
                        @empty
                            <li class="text-sm text-mutedText">Belum ada notifikasi.</li>
                        @endforelse
                    </ul>
                </x-card>

                <x-card>
                    <h3 class="text-2xl text-ink">7 Hari Terakhir</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach ($weeklyCompletionSeries as $point)
                            <li class="grid grid-cols-[88px_1fr_42px] items-center gap-3 text-sm">
                                <span class="text-warmText">{{ \Carbon\Carbon::parse($point['date'])->format('d M') }}</span>
                                <span class="h-2 rounded-full bg-sand">
                                    <span class="block h-2 rounded-full bg-terracotta" style="width: {{ min(100, $point['completed_count'] * 20) }}%"></span>
                                </span>
                                <span class="text-right text-mutedText">{{ $point['completed_count'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            </div>
        </section>
    @endif
@endsection
