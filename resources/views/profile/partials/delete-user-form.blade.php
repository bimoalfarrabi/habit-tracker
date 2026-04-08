<section class="space-y-5">
    <header>
        <h2 class="text-2xl text-ink">Delete Account</h2>
        <p class="mt-1 text-sm text-warmText">
            Tindakan ini permanen. Semua data akun akan dihapus dan tidak bisa dipulihkan.
        </p>
    </header>

    <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4 rounded-soft border border-dangerWarm/30 bg-[#fff8f6] p-4">
        @csrf
        @method('delete')

        <div>
            <label for="delete_account_password" class="text-sm font-medium text-warmText">
                Password Confirmation
            </label>
            <input
                id="delete_account_password"
                name="password"
                type="password"
                class="form-control"
                placeholder="Masukkan password saat ini"
                autocomplete="current-password"
            >

            @if ($errors->userDeletion->has('password'))
                <p class="mt-1 text-xs text-dangerWarm">{{ $errors->userDeletion->first('password') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-button
                type="submit"
                variant="danger"
                onclick="return confirm('Yakin ingin menghapus akun? Semua data akan hilang permanen.')"
            >
                Delete My Account
            </x-button>
            <p class="text-xs text-mutedText">Pastikan tidak ada data penting yang masih dibutuhkan.</p>
        </div>
    </form>
</section>
