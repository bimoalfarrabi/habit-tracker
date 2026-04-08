@extends('layouts.app')

@section('content')
    <x-page-header title="Notifications" subtitle="Pengingat dan informasi yang relevan untuk hari ini.">
        <x-slot:actions>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <x-button type="submit" variant="secondary">Mark All as Read</x-button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <x-card class="mb-4">
        <p class="text-sm text-warmText">Unread notifications: <strong class="text-ink">{{ $unreadCount }}</strong></p>
    </x-card>

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            <x-notification-item :notification="$notification" />
        @empty
            <x-empty-state
                title="Belum ada notifikasi"
                description="Reminder dari cron akan muncul di sini saat waktu habit kamu tiba."
            />
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
@endsection
