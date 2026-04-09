<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FocusSessionController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('habits', HabitController::class);
    Route::post('/habits/{habit}/archive', [HabitController::class, 'archive'])->name('habits.archive');
    Route::post('/habits/{habit}/toggle-active', [HabitController::class, 'toggleActive'])->name('habits.toggle-active');

    Route::post('/habit-logs', [HabitLogController::class, 'store'])->name('habit-logs.store');
    Route::patch('/habit-logs/{habitLog}', [HabitLogController::class, 'update'])->name('habit-logs.update');

    Route::get('/focus-sessions', [FocusSessionController::class, 'index'])->name('focus-sessions.index');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('ajax')->name('ajax.')->group(function (): void {
    Route::post('/focus-sessions/start', [FocusSessionController::class, 'start'])
        ->name('focus-sessions.start');
    Route::post('/focus-sessions/{focusSession}/stop', [FocusSessionController::class, 'stop'])
        ->name('focus-sessions.stop');
    Route::get('/focus-sessions/today-summary', [FocusSessionController::class, 'todaySummary'])
        ->name('focus-sessions.today-summary');

    Route::get('/notifications', [NotificationController::class, 'list'])->name('notifications.list');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
        ->name('notifications.unread-count');

    Route::post('/habit-logs/quick-checkin', [HabitLogController::class, 'quickCheckin'])
        ->name('habit-logs.quick-checkin');

    Route::get('/dashboard/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
    Route::get('/dashboard/today-habits', [DashboardController::class, 'todayHabits'])
        ->name('dashboard.today-habits');
});

require __DIR__.'/auth.php';
