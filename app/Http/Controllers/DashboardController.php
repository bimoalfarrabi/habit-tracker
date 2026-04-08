<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserNotificationResource;
use App\Models\FocusSession;
use App\Services\DashboardStatsService;
use App\Services\NotificationService;
use App\Support\Concerns\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected DashboardStatsService $dashboardStatsService,
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $stats = $this->dashboardStatsService->getForUser($user);
        $todayHabits = $this->dashboardStatsService->getTodayHabitsForUser($user);
        $weeklyCompletionSeries = $this->dashboardStatsService->getWeeklyCompletionSeries($user);
        $latestNotifications = $this->notificationService->getLatestForUser($user, 5);
        $runningSession = FocusSession::query()
            ->where('user_id', $user->id)
            ->where('status', 'running')
            ->latest('id')
            ->first();

        return view('dashboard.index', [
            'stats' => $stats,
            'todayHabits' => $todayHabits,
            'weeklyCompletionSeries' => $weeklyCompletionSeries,
            'latestNotifications' => $latestNotifications,
            'runningSession' => $runningSession,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $stats = $this->dashboardStatsService->getForUser($request->user());

        return $this->successResponse('Dashboard summary loaded.', $stats);
    }

    public function todayHabits(Request $request): JsonResponse
    {
        $habits = $this->dashboardStatsService
            ->getTodayHabitsForUser($request->user())
            ->map(fn (array $item) => [
                'id' => $item['habit']->id,
                'title' => $item['habit']->title,
                'frequency' => $item['habit']->frequency,
                'target_count' => $item['habit']->target_count,
                'reminder_time' => $item['habit']->reminder_time,
                'today_log' => $item['today_log']
                    ? [
                        'id' => $item['today_log']->id,
                        'status' => $item['today_log']->status,
                        'qty' => $item['today_log']->qty,
                    ]
                    : null,
                'is_completed_today' => $item['is_completed_today'],
            ]);

        return $this->successResponse('Today habits loaded.', [
            'habits' => $habits,
            'notifications_preview' => UserNotificationResource::collection(
                $this->notificationService->getLatestForUser($request->user(), 5)
            ),
        ]);
    }
}
