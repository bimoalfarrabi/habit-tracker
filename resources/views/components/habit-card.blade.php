@props([
    'habit',
    'todayLog' => null,
    'isCompletedToday' => false,
])

<x-card class="space-y-4">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="text-2xl font-semibold text-ink">{{ $habit->title }}</h3>
            @if ($habit->description)
                <p class="mt-1 text-sm text-warmText">{{ $habit->description }}</p>
            @endif
        </div>

        <span class="badge-soft {{ $isCompletedToday ? '!bg-[#e7f1e2] !text-[#42624a]' : '' }}" data-habit-status="{{ $habit->id }}">
            {{ $isCompletedToday ? 'Completed' : 'Pending' }}
        </span>
    </div>

    <div class="flex flex-wrap gap-2 text-xs text-warmText">
        <span class="badge-soft">{{ ucfirst($habit->frequency) }}</span>
        <span class="badge-soft">Target {{ $habit->target_count }}</span>
        @if ($habit->reminder_time)
            <span class="badge-soft">Reminder {{ \Carbon\Carbon::parse($habit->reminder_time)->format('H:i') }}</span>
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <x-button variant="primary"
            data-quick-checkin
            data-habit-id="{{ $habit->id }}"
            data-status="completed"
            data-qty="{{ $habit->target_count }}"
        >
            Quick Check-in
        </x-button>

        <a href="{{ route('habits.edit', $habit) }}" class="btn-secondary-warm">Edit</a>

        <form method="POST" action="{{ route('habits.archive', $habit) }}">
            @csrf
            <x-button variant="ghost" type="submit">Archive</x-button>
        </form>
    </div>
</x-card>
