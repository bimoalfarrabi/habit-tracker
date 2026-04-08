@extends('layouts.app')

@section('content')
    <x-page-header title="Focus Session" subtitle="Catat sesi fokus tanpa membuat ritme terasa kaku." />

    <div
        data-focus-page
        data-running-session-id="{{ $runningSession?->id }}"
        data-running-start-time="{{ $runningSession?->start_time?->toISOString() }}"
        data-running-focused="{{ $runningSession?->focused_duration_seconds ?? 0 }}"
        data-running-unfocused="{{ $runningSession?->unfocused_duration_seconds ?? 0 }}"
        data-running-interruptions="{{ $runningSession?->interruption_count ?? 0 }}"
        class="grid gap-6 lg:grid-cols-[1.25fr_1fr]"
    >
        <x-card variant="dark" class="space-y-4">
            <p class="text-sm uppercase tracking-[0.15em] text-[#c9c4bb]">Timer</p>
            <p data-focus-elapsed class="text-6xl font-semibold">00:00:00</p>
            <p class="text-sm text-[#c9c4bb]">Status: <span data-focus-status>{{ $runningSession ? 'Running' : 'Idle' }}</span></p>

            <div class="grid gap-2 text-sm text-[#ded7cd] sm:grid-cols-2">
                <p>Focused: <span data-focus-focused>{{ floor(($runningSession?->focused_duration_seconds ?? 0) / 60) }} min</span></p>
                <p>Background: <span data-focus-unfocused>{{ floor(($runningSession?->unfocused_duration_seconds ?? 0) / 60) }} min</span></p>
            </div>

            <p class="text-xs text-[#c9c4bb]">
                Timer tetap berjalan saat halaman tidak aktif. Sistem akan memisahkan waktu focused dan background.
            </p>

            <button data-focus-stop class="btn-secondary-warm">Stop Session</button>
        </x-card>

        <x-card>
            <h3 class="text-2xl text-ink">Start Session</h3>

            <form data-focus-start-form class="mt-4 space-y-3">
                <div>
                    <label class="text-sm font-medium text-warmText" for="habit_id">Related Habit (optional)</label>
                    <select id="habit_id" name="habit_id" class="form-control">
                        <option value="">General Focus</option>
                        @foreach ($habits as $habit)
                            <option value="{{ $habit->id }}">{{ $habit->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-warmText" for="planned_duration_minutes">Planned Duration (minutes)</label>
                    <input id="planned_duration_minutes" name="planned_duration_minutes" type="number" min="1" max="1440" class="form-control" value="25">
                </div>

                <div>
                    <label class="text-sm font-medium text-warmText" for="note">Note</label>
                    <textarea id="note" name="note" rows="3" class="form-control" placeholder="Contoh: Deep work pagi"></textarea>
                </div>

                <x-button type="submit">Start Focus</x-button>
            </form>

            <div class="mt-5 rounded-soft bg-sand p-3 text-sm text-warmText">
                <p>Total sessions today: {{ $todaySummary['total_sessions'] }}</p>
                <p>Focus minutes today: {{ $todaySummary['focus_minutes_today'] }}</p>
                <p>Background minutes today: {{ $todaySummary['background_minutes_today'] }}</p>
            </div>
        </x-card>
    </div>

    <x-card class="mt-6">
        <h3 class="text-2xl text-ink">Session History</h3>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-left text-sm text-warmText">
                <thead>
                    <tr class="border-b border-borderCream text-xs uppercase tracking-[0.12em] text-mutedText">
                        <th class="py-2">Date</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Focused</th>
                        <th class="py-2">Background</th>
                        <th class="py-2">Interruptions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sessions as $session)
                        <tr class="border-b border-borderCream/70">
                            <td class="py-2">{{ $session->session_date?->format('d M Y') }}</td>
                            <td class="py-2">{{ ucfirst($session->status) }}</td>
                            <td class="py-2">{{ floor($session->focused_duration_seconds / 60) }} min</td>
                            <td class="py-2">{{ floor($session->unfocused_duration_seconds / 60) }} min</td>
                            <td class="py-2">{{ $session->interruption_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-3 text-mutedText">Belum ada history sesi fokus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $sessions->links() }}
        </div>
    </x-card>
@endsection
