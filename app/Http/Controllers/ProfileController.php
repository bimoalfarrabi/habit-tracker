<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\FocusSession;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\UserNotification;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $today = now()->toDateString();
        $focusMinutes = (int) floor(
            FocusSession::query()
                ->where('user_id', $user->id)
                ->sum('focused_duration_seconds') / 60
        );

        return view('profile.edit', [
            'user' => $user,
            'profileStats' => [
                'total_habits' => Habit::query()->where('user_id', $user->id)->count(),
                'active_habits' => Habit::query()->where('user_id', $user->id)->active()->count(),
                'completed_today' => HabitLog::query()
                    ->where('user_id', $user->id)
                    ->completed()
                    ->forDate($today)
                    ->count(),
                'focus_minutes' => $focusMinutes,
                'total_focus_sessions' => FocusSession::query()
                    ->where('user_id', $user->id)
                    ->count(),
            ],
            'recentActivities' => $this->buildRecentActivities($user->id),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->boolean('remove_photo') && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
        }

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * @return Collection<int, array{type:string,title:string,description:string,occurred_at:\Illuminate\Support\Carbon}>
     */
    private function buildRecentActivities(int $userId): Collection
    {
        $habitActivities = Habit::query()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(fn (Habit $habit): array => [
                'type' => 'Habit',
                'title' => 'Membuat habit baru',
                'description' => $habit->title,
                'occurred_at' => $habit->created_at,
            ]);

        $habitLogActivities = HabitLog::query()
            ->with('habit:id,title')
            ->where('user_id', $userId)
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(fn (HabitLog $habitLog): array => [
                'type' => 'Check-in',
                'title' => 'Update progres habit',
                'description' => sprintf(
                    '%s (%s)',
                    $habitLog->habit?->title ?? 'Habit',
                    ucfirst($habitLog->status)
                ),
                'occurred_at' => $habitLog->created_at,
            ]);

        $focusActivities = FocusSession::query()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(fn (FocusSession $session): array => [
                'type' => 'Focus',
                'title' => 'Menyelesaikan focus session',
                'description' => floor($session->focused_duration_seconds / 60).' menit fokus',
                'occurred_at' => $session->created_at,
            ]);

        $notificationActivities = UserNotification::query()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(fn (UserNotification $notification): array => [
                'type' => 'Notification',
                'title' => $notification->title,
                'description' => $notification->message,
                'occurred_at' => $notification->created_at,
            ]);

        return $habitActivities
            ->merge($habitLogActivities)
            ->merge($focusActivities)
            ->merge($notificationActivities)
            ->sortByDesc('occurred_at')
            ->take(20)
            ->values();
    }
}
