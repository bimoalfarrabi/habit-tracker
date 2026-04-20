<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthProvider;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! $this->isGoogleConfigured()) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Google Sign-In belum dikonfigurasi. Hubungi admin aplikasi.',
            ]);
        }

        return Socialite::driver(AuthProvider::PROVIDER_GOOGLE)->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! $this->isGoogleConfigured()) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Google Sign-In belum dikonfigurasi. Hubungi admin aplikasi.',
            ]);
        }

        try {
            /** @var AbstractUser $googleUser */
            $googleUser = Socialite::driver(AuthProvider::PROVIDER_GOOGLE)->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->withErrors([
                'oauth' => 'Autentikasi Google gagal. Silakan coba lagi.',
            ]);
        }

        $googleUserId = trim((string) $googleUser->getId());
        $googleEmail = Str::lower(trim((string) $googleUser->getEmail()));

        if ($googleUserId === '' || $googleEmail === '') {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Akun Google tidak menyediakan email yang valid.',
            ]);
        }

        if (! $this->isGoogleEmailVerified($googleUser)) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Email Google harus terverifikasi sebelum bisa digunakan untuk login.',
            ]);
        }

        $user = DB::transaction(function () use ($googleUser, $googleUserId, $googleEmail): User {
            return $this->resolveUser($googleUser, $googleUserId, $googleEmail);
        });

        Auth::login($user, true);

        $request->session()->regenerate();

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function resolveUser(AbstractUser $googleUser, string $googleUserId, string $googleEmail): User
    {
        $provider = AuthProvider::query()
            ->where('provider', AuthProvider::PROVIDER_GOOGLE)
            ->where('provider_user_id', $googleUserId)
            ->first();

        if ($provider) {
            $provider->forceFill([
                'provider_email' => $googleEmail,
                'provider_email_verified_at' => now(),
            ])->save();

            $user = $provider->user;

            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return $user;
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$googleEmail])
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $this->resolveDisplayName($googleUser, $googleEmail),
                'email' => $googleEmail,
                'password' => Hash::make(Str::random(40)),
            ]);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        AuthProvider::query()->create([
            'user_id' => $user->id,
            'provider' => AuthProvider::PROVIDER_GOOGLE,
            'provider_user_id' => $googleUserId,
            'provider_email' => $googleEmail,
            'provider_email_verified_at' => now(),
        ]);

        return $user;
    }

    private function resolveDisplayName(AbstractUser $googleUser, string $googleEmail): string
    {
        $name = trim((string) $googleUser->getName());

        if ($name !== '') {
            return $name;
        }

        return Str::of($googleEmail)
            ->before('@')
            ->replace(['.', '_', '-'], ' ')
            ->title()
            ->value();
    }

    private function isGoogleEmailVerified(AbstractUser $googleUser): bool
    {
        $raw = $googleUser->getRaw();
        $emailVerifiedValue = $raw['email_verified'] ?? $raw['verified_email'] ?? true;

        if (is_bool($emailVerifiedValue)) {
            return $emailVerifiedValue;
        }

        $parsed = filter_var($emailVerifiedValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $parsed ?? false;
    }

    private function isGoogleConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }
}
