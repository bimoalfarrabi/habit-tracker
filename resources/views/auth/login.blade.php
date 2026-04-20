@extends('layouts.guest')

@section('content')
    <div class="mb-6 text-center">
        <p class="text-xs uppercase tracking-[0.15em] text-mutedText">Welcome Back</p>
        <h1 class="mt-2 text-5xl text-ink">Masuk ke Ritme</h1>
        <p class="mt-2 text-sm text-warmText">Bangun kebiasaan kecil setiap hari, dengan langkah yang konsisten.</p>
    </div>

    @if ($errors->has('oauth'))
        <div class="mb-4 rounded-soft border border-[#dfc4c4] bg-[#fbefef] px-4 py-3 text-sm text-dangerWarm">
            {{ $errors->first('oauth') }}
        </div>
    @endif

    <a href="{{ route('auth.google.redirect') }}" class="btn-secondary-warm w-full">
        Continue with Google
    </a>

    <div class="my-4 flex items-center gap-3 text-xs uppercase tracking-[0.12em] text-mutedText">
        <span class="h-px flex-1 bg-borderCream"></span>
        <span>atau</span>
        <span class="h-px flex-1 bg-borderCream"></span>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="text-sm font-medium text-warmText">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control">
            @error('email') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="text-sm font-medium text-warmText">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-warmText hover:text-ink">Forgot password?</a>
                @endif
            </div>
            <input id="password" type="password" name="password" required class="form-control">
            @error('password') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-warmText">
            <input type="checkbox" name="remember" class="rounded border-borderCream text-terracotta focus:ring-focusBlue">
            Remember me
        </label>

        <x-button type="submit" class="w-full">Login</x-button>
    </form>

    <p class="mt-5 text-center text-sm text-warmText">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-ink">Daftar sekarang</a>
    </p>
@endsection
