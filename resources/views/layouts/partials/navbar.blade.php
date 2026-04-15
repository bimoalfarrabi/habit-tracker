<nav
    x-data="{
        mobileMenuOpen: false,
        desktopMenu: null,
        toggleDesktopMenu(menu) {
            this.desktopMenu = this.desktopMenu === menu ? null : menu;
        },
    }"
    x-on:keydown.escape.window="desktopMenu = null; mobileMenuOpen = false"
    class="border-b border-borderCream bg-ivory/90 backdrop-blur"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 lg:px-8">
        <a href="{{ route('dashboard') }}" class="text-2xl font-semibold tracking-wide text-ink">Ritme</a>

        @auth
            @php
                $moreActive = request()->routeIs('focus-sessions.*')
                    || request()->routeIs('notifications.*')
                    || request()->routeIs('settings.*');
                $adminActive = request()->routeIs('admin.welcome-content.*')
                    || request()->routeIs('admin.users.*');
                $accountActive = request()->routeIs('profile.*');
            @endphp

            <div class="hidden items-center gap-1 rounded-full bg-sand px-2 py-1 md:flex">
                <a href="{{ route('dashboard') }}" class="btn-ghost-warm {{ request()->routeIs('dashboard') ? 'bg-ivory text-ink' : '' }}">Dashboard</a>
                <a href="{{ route('habits.index') }}" class="btn-ghost-warm {{ request()->routeIs('habits.*') ? 'bg-ivory text-ink' : '' }}">Habits</a>
                <a href="{{ route('todos.index') }}" class="btn-ghost-warm {{ request()->routeIs('todos.*') ? 'bg-ivory text-ink' : '' }}">Todos</a>

                <div class="relative" x-on:click.outside="desktopMenu = null">
                    <button
                        type="button"
                        class="btn-ghost-warm {{ $moreActive ? 'bg-ivory text-ink' : '' }}"
                        x-on:click.stop="toggleDesktopMenu('more')"
                        :aria-expanded="desktopMenu === 'more' ? 'true' : 'false'"
                    >
                        <span>More</span>
                    </button>
                    <div
                        class="absolute right-0 z-30 mt-2 w-56 overflow-hidden rounded-soft border border-borderCream bg-ivory shadow-whisper"
                        style="display: none;"
                        x-show="desktopMenu === 'more'"
                        x-transition.duration.120ms
                    >
                        <a href="{{ route('focus-sessions.index') }}" x-on:click="desktopMenu = null" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('focus-sessions.*') ? 'bg-sand text-ink' : '' }}">Focus Sessions</a>
                        <a href="{{ route('notifications.index') }}" x-on:click="desktopMenu = null" class="flex items-center justify-between px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('notifications.*') ? 'bg-sand text-ink' : '' }}">
                            <span>Notifications</span>
                            <span data-unread-badge class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-terracotta px-1.5 py-0.5 text-[10px] font-semibold text-ivory {{ $unreadNotificationCount < 1 ? 'hidden' : '' }}">
                                {{ $unreadNotificationCount }}
                            </span>
                        </a>
                        <a href="{{ route('settings.index') }}" x-on:click="desktopMenu = null" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('settings.*') ? 'bg-sand text-ink' : '' }}">Settings</a>
                    </div>
                </div>

                @if (auth()->user()->isAdmin())
                    <div class="relative" x-on:click.outside="desktopMenu = null">
                        <button
                            type="button"
                            class="btn-ghost-warm {{ $adminActive ? 'bg-ivory text-ink' : '' }}"
                            x-on:click.stop="toggleDesktopMenu('admin')"
                            :aria-expanded="desktopMenu === 'admin' ? 'true' : 'false'"
                        >
                            <span>Admin</span>
                        </button>
                        <div
                            class="absolute right-0 z-30 mt-2 w-56 overflow-hidden rounded-soft border border-borderCream bg-ivory shadow-whisper"
                            style="display: none;"
                            x-show="desktopMenu === 'admin'"
                            x-transition.duration.120ms
                        >
                            <a href="{{ route('admin.welcome-content.edit') }}" x-on:click="desktopMenu = null" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('admin.welcome-content.*') ? 'bg-sand text-ink' : '' }}">CMS Welcome</a>
                            <a href="{{ route('admin.users.index') }}" x-on:click="desktopMenu = null" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('admin.users.*') ? 'bg-sand text-ink' : '' }}">User Management</a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <div class="relative hidden md:block" x-on:click.outside="desktopMenu = null">
                    <button
                        type="button"
                        class="btn-secondary-warm {{ $accountActive ? 'ring-2 ring-focusBlue/40' : '' }}"
                        x-on:click.stop="toggleDesktopMenu('account')"
                        :aria-expanded="desktopMenu === 'account' ? 'true' : 'false'"
                    >
                        <span>{{ auth()->user()->name }}</span>
                    </button>
                    <div
                        class="absolute right-0 z-30 mt-2 w-44 overflow-hidden rounded-soft border border-borderCream bg-ivory shadow-whisper"
                        style="display: none;"
                        x-show="desktopMenu === 'account'"
                        x-transition.duration.120ms
                    >
                        <a href="{{ route('profile.edit') }}" x-on:click="desktopMenu = null" class="block px-3 py-2 text-sm text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('profile.*') ? 'bg-sand text-ink' : '' }}">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="block w-full px-3 py-2 text-left text-sm text-warmText transition hover:bg-sand hover:text-ink" x-on:click="desktopMenu = null" type="submit">Logout</button>
                        </form>
                    </div>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-soft bg-sand p-2 text-warmText transition hover:bg-[#dfddd2] hover:text-ink md:hidden"
                    x-on:click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen ? 'true' : 'false'"
                    aria-controls="mobile-main-menu"
                    aria-label="Toggle mobile menu"
                >
                    <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm1 4a1 1 0 100 2h12a1 1 0 100-2H4z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" style="display: none;">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endauth
    </div>

    @auth
        <div
            id="mobile-main-menu"
            class="border-t border-borderCream px-4 pb-4 pt-3 md:hidden"
            style="display: none;"
            x-show="mobileMenuOpen"
            x-transition.duration.150ms
        >
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" x-on:click="mobileMenuOpen = false" class="block rounded-soft px-3 py-2 text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('dashboard') ? 'bg-sand text-ink' : '' }}">Dashboard</a>
                <a href="{{ route('habits.index') }}" x-on:click="mobileMenuOpen = false" class="block rounded-soft px-3 py-2 text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('habits.*') ? 'bg-sand text-ink' : '' }}">Habits</a>
                <a href="{{ route('todos.index') }}" x-on:click="mobileMenuOpen = false" class="block rounded-soft px-3 py-2 text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('todos.*') ? 'bg-sand text-ink' : '' }}">Todos</a>
            </div>

            <div class="mt-3 border-t border-borderCream pt-3">
                <p class="px-3 text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">More</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('focus-sessions.index') }}" x-on:click="mobileMenuOpen = false" class="block rounded-soft px-3 py-2 text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('focus-sessions.*') ? 'bg-sand text-ink' : '' }}">Focus Sessions</a>
                    <a href="{{ route('notifications.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center justify-between rounded-soft px-3 py-2 text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('notifications.*') ? 'bg-sand text-ink' : '' }}">
                        <span>Notifications</span>
                        <span data-unread-badge class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-terracotta px-1.5 py-0.5 text-[10px] font-semibold text-ivory {{ $unreadNotificationCount < 1 ? 'hidden' : '' }}">
                            {{ $unreadNotificationCount }}
                        </span>
                    </a>
                    <a href="{{ route('settings.index') }}" x-on:click="mobileMenuOpen = false" class="block rounded-soft px-3 py-2 text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('settings.*') ? 'bg-sand text-ink' : '' }}">Settings</a>
                </div>
            </div>

            @if (auth()->user()->isAdmin())
                <div class="mt-3 border-t border-borderCream pt-3">
                    <p class="px-3 text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Admin</p>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('admin.welcome-content.edit') }}" x-on:click="mobileMenuOpen = false" class="block rounded-soft px-3 py-2 text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('admin.welcome-content.*') ? 'bg-sand text-ink' : '' }}">CMS Welcome</a>
                        <a href="{{ route('admin.users.index') }}" x-on:click="mobileMenuOpen = false" class="block rounded-soft px-3 py-2 text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('admin.users.*') ? 'bg-sand text-ink' : '' }}">User Management</a>
                    </div>
                </div>
            @endif

            <div class="mt-3 border-t border-borderCream pt-3">
                <p class="px-3 text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Account</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('profile.edit') }}" x-on:click="mobileMenuOpen = false" class="block rounded-soft px-3 py-2 text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink {{ request()->routeIs('profile.*') ? 'bg-sand text-ink' : '' }}">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="block w-full rounded-soft px-3 py-2 text-left text-sm font-medium text-warmText transition hover:bg-sand hover:text-ink" x-on:click="mobileMenuOpen = false" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    @endauth
</nav>
