@extends('layouts.guest')

@section('content')
    <div class="mb-6 text-center">
        <p class="text-xs uppercase tracking-[0.15em] text-mutedText">Create Account</p>
        <h1 class="mt-2 text-5xl text-ink">Mulai Ritme Baru</h1>
        <p class="mt-2 text-sm text-warmText">Satu langkah kecil hari ini bisa jadi perubahan besar nanti.</p>
    </div>

    @if ($errors->has('oauth'))
        <div class="mb-4 rounded-soft border border-[#dfc4c4] bg-[#fbefef] px-4 py-3 text-sm text-dangerWarm">
            {{ $errors->first('oauth') }}
        </div>
    @endif

    <a href="{{ route('auth.google.redirect') }}" class="btn-secondary-warm w-full">
        Sign up with Google
    </a>

    <div class="my-4 flex items-center gap-3 text-xs uppercase tracking-[0.12em] text-mutedText">
        <span class="h-px flex-1 bg-borderCream"></span>
        <span>atau</span>
        <span class="h-px flex-1 bg-borderCream"></span>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="text-sm font-medium text-warmText">Name</label>
            <input id="name" name="name" value="{{ old('name') }}" required autofocus class="form-control">
            @error('name') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="text-sm font-medium text-warmText">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-control">
            @error('email') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="text-sm font-medium text-warmText">Password</label>
            <input id="password" type="password" name="password" required class="form-control">
            @error('password') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="text-sm font-medium text-warmText">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
        </div>

        <x-button type="submit" class="w-full">Create Account</x-button>
    </form>

    <p class="mt-5 text-center text-sm text-warmText">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-ink">Masuk di sini</a>
    </p>
@endsection
