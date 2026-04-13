<?php

namespace App\Providers;

use App\Models\UserNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $verificationUrl) {
            return (new MailMessage)
                ->subject('Verifikasi Email Akun Ritme')
                ->view('emails.auth.verify-email', [
                    'verificationUrl' => $verificationUrl,
                    'userName' => $notifiable->name ?? 'Teman Ritme',
                    'expireMinutes' => (int) config('auth.verification.expire', 60),
                ]);
        });

        View::composer('*', function ($view): void {
            $unreadNotificationCount = 0;

            if (auth()->check()) {
                $unreadNotificationCount = UserNotification::query()
                    ->where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
            }

            $view->with('unreadNotificationCount', $unreadNotificationCount);
        });
    }
}
