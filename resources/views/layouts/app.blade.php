<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head', ['title' => $title ?? null])
</head>
<body>
    <div class="relative min-h-screen">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(201,100,66,0.08),_transparent_45%)]"></div>

        @include('layouts.partials.navbar')

        <main class="relative page-shell" data-page-transition>
            @include('layouts.partials.flash-message')

            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
    </div>

    @include('layouts.partials.footer')
    @stack('scripts')
</body>
</html>
