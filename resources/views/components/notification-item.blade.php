@props([
    'notification',
])

<div class="rounded-soft border border-borderCream bg-ivory p-4 {{ $notification->is_read ? 'opacity-80' : '' }}">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-ink">{{ $notification->title }}</p>
            <p class="mt-1 text-sm text-warmText">{{ $notification->message }}</p>
            <p class="mt-2 text-xs text-mutedText">{{ $notification->created_at?->diffForHumans() }}</p>
        </div>

        @if (! $notification->is_read)
            <form method="POST" action="{{ route('notifications.read', $notification) }}" class="shrink-0">
                @csrf
                <x-button variant="ghost" type="submit">Mark as read</x-button>
            </form>
        @endif
    </div>
</div>
