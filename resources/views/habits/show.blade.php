@extends('layouts.app')

@section('content')
    <x-page-header :title="$habit->title" subtitle="Detail habit dan riwayat 30 hari terakhir." />

    <x-card class="mb-5">
        <div class="grid gap-3 text-sm text-warmText md:grid-cols-2">
            <p><span class="font-semibold text-ink">Frequency:</span> {{ ucfirst($habit->frequency) }}</p>
            <p><span class="font-semibold text-ink">Target:</span> {{ $habit->target_count }}</p>
            <p><span class="font-semibold text-ink">Reminder:</span> {{ $habit->reminder_time ? \Carbon\Carbon::parse($habit->reminder_time)->format('H:i') : '-' }}</p>
            <p><span class="font-semibold text-ink">Status:</span> {{ $habit->is_active ? 'Active' : 'Inactive' }}</p>
        </div>
    </x-card>

    <x-card>
        <h3 class="text-2xl text-ink">Recent Logs</h3>
        <ul class="mt-3 space-y-2">
            @forelse ($habit->logs as $log)
                <li class="rounded-soft border border-borderCream px-3 py-2 text-sm text-warmText">
                    {{ $log->log_date?->format('d M Y') }} · {{ ucfirst($log->status) }} · Qty {{ $log->qty }}
                </li>
            @empty
                <li class="text-sm text-mutedText">Belum ada log untuk habit ini.</li>
            @endforelse
        </ul>
    </x-card>
@endsection
