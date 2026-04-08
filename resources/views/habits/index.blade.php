@extends('layouts.app')

@section('content')
    <x-page-header title="Habits" subtitle="Kelola kebiasaan yang ingin kamu jaga.">
        <x-slot:actions>
            <a href="{{ route('habits.create') }}" class="btn-primary-warm">+ Create Habit</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-5 flex flex-wrap gap-2">
        @foreach (['all' => 'All', 'active' => 'Active', 'archived' => 'Archived'] as $key => $label)
            <a href="{{ route('habits.index', ['filter' => $key]) }}" class="{{ $filter === $key ? 'btn-primary-warm' : 'btn-secondary-warm' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse ($habits as $habit)
            @php
                $todayLog = $habit->logs->first();
            @endphp
            <x-habit-card
                :habit="$habit"
                :today-log="$todayLog"
                :is-completed-today="(bool) ($todayLog && $todayLog->status === 'completed')"
            />
        @empty
            <x-empty-state
                title="Belum ada habit"
                description="Tambahkan kebiasaan pertama kamu untuk mulai membangun ritme harian."
            >
                <x-slot:action>
                    <a href="{{ route('habits.create') }}" class="btn-primary-warm">Buat Habit</a>
                </x-slot:action>
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $habits->links() }}
    </div>
@endsection
