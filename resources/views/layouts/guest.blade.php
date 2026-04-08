<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head', ['title' => $title ?? null])
</head>
<body>
    <div class="relative flex min-h-screen items-center justify-center px-4 py-10">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(201,100,66,0.12),_transparent_40%)]"></div>

        <div class="relative w-full max-w-md card-soft p-8">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </div>
    </div>
</body>
</html>
