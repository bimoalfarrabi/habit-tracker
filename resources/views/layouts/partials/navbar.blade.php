<nav class="border-b border-borderCream bg-ivory/90 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 lg:px-8">
        <a href="{{ route('dashboard') }}" class="text-2xl font-semibold tracking-wide text-ink">Ritme</a>

        @auth
            <div class="hidden items-center gap-1 rounded-full bg-sand px-2 py-1 md:flex">
                <a href="{{ route('dashboard') }}" class="btn-ghost-warm {{ request()->routeIs('dashboard') ? 'bg-ivory text-ink' : '' }}">Dashboard</a>
                <a href="{{ route('habits.index') }}" class="btn-ghost-warm {{ request()->routeIs('habits.*') ? 'bg-ivory text-ink' : '' }}">Habits</a>
                <a href="{{ route('todos.index') }}" class="btn-ghost-warm {{ request()->routeIs('todos.*') ? 'bg-ivory text-ink' : '' }}">Todos</a>
                <a href="{{ route('focus-sessions.index') }}" class="btn-ghost-warm {{ request()->routeIs('focus-sessions.*') ? 'bg-ivory text-ink' : '' }}">Focus</a>
                <a href="{{ route('notifications.index') }}" class="btn-ghost-warm {{ request()->routeIs('notifications.*') ? 'bg-ivory text-ink' : '' }}">
                    Notifications
                    <span data-unread-badge class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-terracotta px-1.5 py-0.5 text-[10px] font-semibold text-ivory {{ $unreadNotificationCount < 1 ? 'hidden' : '' }}">
                        {{ $unreadNotificationCount }}
                    </span>
                </a>
                <a href="{{ route('profile.edit') }}" class="btn-ghost-warm {{ request()->routeIs('profile.*') ? 'bg-ivory text-ink' : '' }}">Profile</a>
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden text-sm text-warmText md:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn-secondary-warm" type="submit">Logout</button>
                </form>
            </div>
        @endauth
    </div>
</nav>
