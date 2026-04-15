<nav class="border-b border-borderCream bg-ivory/90 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 lg:px-8">
        <a href="{{ route('dashboard') }}" class="text-2xl font-semibold tracking-wide text-ink">Ritme</a>

        @auth
            @php
                $moreActive = request()->routeIs('focus-sessions.*')
                    || request()->routeIs('notifications.*')
                    || request()->routeIs('settings.*');
                $adminActive = request()->routeIs('admin.welcome-content.*')
                    || request()->routeIs('admin.users.*');
            @endphp

            <div class="hidden items-center gap-1 rounded-full bg-sand px-2 py-1 md:flex">
                <a href="{{ route('dashboard') }}" class="btn-ghost-warm {{ request()->routeIs('dashboard') ? 'bg-ivory text-ink' : '' }}">Dashboard</a>
                <a href="{{ route('habits.index') }}" class="btn-ghost-warm {{ request()->routeIs('habits.*') ? 'bg-ivory text-ink' : '' }}">Habits</a>
                <a href="{{ route('todos.index') }}" class="btn-ghost-warm {{ request()->routeIs('todos.*') ? 'bg-ivory text-ink' : '' }}">Todos</a>

                <details class="relative">
                    <summary class="btn-ghost-warm list-none [&::-webkit-details-marker]:hidden {{ $moreActive ? 'bg-ivory text-ink' : '' }}">
                        <span>More</span>
                    </summary>
                    <div class="absolute right-0 z-20 mt-2 w-56 overflow-hidden rounded-soft border border-borderCream bg-ivory shadow-whisper">
                        <a href="{{ route('focus-sessions.index') }}" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('focus-sessions.*') ? 'bg-sand text-ink' : '' }}">Focus Sessions</a>
                        <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('notifications.*') ? 'bg-sand text-ink' : '' }}">
                            <span>Notifications</span>
                            <span data-unread-badge class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-terracotta px-1.5 py-0.5 text-[10px] font-semibold text-ivory {{ $unreadNotificationCount < 1 ? 'hidden' : '' }}">
                                {{ $unreadNotificationCount }}
                            </span>
                        </a>
                        <a href="{{ route('settings.index') }}" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('settings.*') ? 'bg-sand text-ink' : '' }}">Settings</a>
                    </div>
                </details>

                @if (auth()->user()->isAdmin())
                    <details class="relative">
                        <summary class="btn-ghost-warm list-none [&::-webkit-details-marker]:hidden {{ $adminActive ? 'bg-ivory text-ink' : '' }}">
                            <span>Admin</span>
                        </summary>
                        <div class="absolute right-0 z-20 mt-2 w-56 overflow-hidden rounded-soft border border-borderCream bg-ivory shadow-whisper">
                            <a href="{{ route('admin.welcome-content.edit') }}" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('admin.welcome-content.*') ? 'bg-sand text-ink' : '' }}">CMS Welcome</a>
                            <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('admin.users.*') ? 'bg-sand text-ink' : '' }}">User Management</a>
                        </div>
                    </details>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <details class="relative">
                    <summary class="btn-secondary-warm list-none [&::-webkit-details-marker]:hidden">
                        <span class="hidden md:inline">{{ auth()->user()->name }}</span>
                        <span class="md:hidden">Account</span>
                    </summary>
                    <div class="absolute right-0 z-20 mt-2 w-44 overflow-hidden rounded-soft border border-borderCream bg-ivory shadow-whisper">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('profile.*') ? 'bg-sand text-ink' : '' }}">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="block w-full px-3 py-2 text-left text-sm text-warmText transition hover:bg-sand hover:text-ink" type="submit">Logout</button>
                        </form>
                    </div>
                </details>
            </div>
        @endauth
    </div>
</nav>
