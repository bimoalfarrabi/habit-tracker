@extends('layouts.guest')

@section('content')
    <div class="mb-6 text-center">
        <p class="text-xs uppercase tracking-[0.15em] text-mutedText">Create Account</p>
        <h1 class="mt-2 text-5xl text-ink">Mulai Ritme Baru</h1>
        <p class="mt-2 text-sm text-warmText">Satu langkah kecil hari ini bisa jadi perubahan besar nanti.</p>
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
