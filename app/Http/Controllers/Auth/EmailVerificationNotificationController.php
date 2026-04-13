<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationCooldownService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class EmailVerificationNotificationController extends Controller
{
    public function __construct(
        protected EmailVerificationCooldownService $emailVerificationCooldownService
    ) {}

    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $remainingCooldown = $this->emailVerificationCooldownService->getRemainingSeconds($request->user());

        if ($remainingCooldown > 0) {
            return back()
                ->with('error', "Tunggu {$remainingCooldown} detik sebelum kirim ulang email verifikasi.")
                ->with('verification_cooldown_seconds', $remainingCooldown);
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Gagal mengirim email verifikasi. Cek konfigurasi SMTP lalu coba lagi.');
        }

        $cooldownSeconds = $this->emailVerificationCooldownService->markSent($request->user());

        return back()
            ->with('status', 'verification-link-sent')
            ->with('verification_cooldown_seconds', $cooldownSeconds);
    }
}
