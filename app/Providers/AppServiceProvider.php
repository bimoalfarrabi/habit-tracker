<?php

namespace App\Providers;

use App\Models\UserNotification;
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
