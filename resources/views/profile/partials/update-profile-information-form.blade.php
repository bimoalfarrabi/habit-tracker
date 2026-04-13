<section class="space-y-5">
    @php
        $cooldownSeconds = (int) ($verificationCooldownSeconds ?? session('verification_cooldown_seconds', 0));
    @endphp

    <header>
        <h2 class="text-2xl text-ink">Profile Information</h2>
        <p class="mt-1 text-sm text-warmText">
            Perbarui nama dan email akun kamu.
        </p>
    </header>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="text-sm font-medium text-warmText">Name</label>
            <input
                id="name"
                name="name"
                type="text"
                class="form-control"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            >
            @error('name')
                <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="text-sm font-medium text-warmText">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                class="form-control"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            >
            @error('email')
                <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 rounded-soft border border-borderCream bg-sand p-3">
                    <p class="text-sm text-warmText">
                        Email kamu belum terverifikasi.
                        <button
                            form="send-verification"
                            class="ml-1 text-sm font-semibold text-ink underline underline-offset-4 hover:text-terracotta disabled:cursor-not-allowed disabled:opacity-60"
                            type="submit"
                            @disabled($cooldownSeconds > 0)
                        >
                            {{ $cooldownSeconds > 0 ? "Tunggu {$cooldownSeconds} detik" : 'Kirim ulang link verifikasi' }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-semibold text-emerald-700">
                            Link verifikasi baru sudah dikirim ke email kamu.
                        </p>
                    @endif

                    @if ($cooldownSeconds > 0)
                        <p class="mt-2 text-xs font-semibold text-amber-700">
                            Cooldown aktif: kirim ulang tersedia lagi dalam {{ $cooldownSeconds }} detik.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <label for="photo" class="text-sm font-medium text-warmText">Foto Profile</label>
            <input
                id="photo"
                name="photo"
                type="file"
                class="form-control file:mr-3 file:rounded-soft file:border-0 file:bg-sand file:px-3 file:py-2 file:text-sm file:font-semibold file:text-ink hover:file:bg-[#dfddd2]"
                accept=".jpg,.jpeg,.png,.webp"
            >
            <p class="mt-1 text-xs text-mutedText">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
            @error('photo')
                <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p>
            @enderror

            @if ($user->profile_photo_path)
                <label class="mt-2 inline-flex items-center gap-2 text-sm text-warmText">
                    <input
                        type="checkbox"
                        name="remove_photo"
                        value="1"
                        @checked((bool) old('remove_photo'))
                        class="rounded border-borderCream text-terracotta focus:ring-focusBlue"
                    >
                    Hapus foto profile saat ini
                </label>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-button type="submit">Save Changes</x-button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-warmText">Perubahan profile sudah disimpan.</p>
            @endif
        </div>
    </form>
</section>
