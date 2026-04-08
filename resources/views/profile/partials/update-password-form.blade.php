<section class="space-y-5">
    <header>
        <h2 class="text-2xl text-ink">Update Password</h2>
        <p class="mt-1 text-sm text-warmText">
            Gunakan password yang kuat agar akun tetap aman.
        </p>
    </header>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="text-sm font-medium text-warmText">Current Password</label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="form-control"
                autocomplete="current-password"
            >
            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-1 text-xs text-dangerWarm">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="text-sm font-medium text-warmText">New Password</label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                class="form-control"
                autocomplete="new-password"
            >
            @if ($errors->updatePassword->has('password'))
                <p class="mt-1 text-xs text-dangerWarm">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="text-sm font-medium text-warmText">Confirm Password</label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="form-control"
                autocomplete="new-password"
            >
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="mt-1 text-xs text-dangerWarm">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-button type="submit">Save Password</x-button>
            @if (session('status') === 'password-updated')
                <p class="text-sm text-warmText">Password berhasil diperbarui.</p>
            @endif
        </div>
    </form>
</section>
