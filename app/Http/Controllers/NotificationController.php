<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notification\MarkNotificationAsReadRequest;
use App\Http\Resources\UserNotificationResource;
use App\Models\UserNotification;
use App\Services\NotificationService;
use App\Support\Concerns\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): View
    {
        $notifications = UserNotification::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(15);

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $this->notificationService->getUnreadCount($request->user()),
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $limit = (int) min(max((int) $request->query('limit', 10), 1), 50);
        $notifications = $this->notificationService->getLatestForUser($request->user(), $limit);

        return $this->successResponse('Notifications loaded.', [
            'notifications' => UserNotificationResource::collection($notifications),
            'unread_count' => $this->notificationService->getUnreadCount($request->user()),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return $this->successResponse('Unread notification count loaded.', [
            'unread_count' => $this->notificationService->getUnreadCount($request->user()),
        ]);
    }

    public function markAsRead(
        MarkNotificationAsReadRequest $request,
        UserNotification $notification
    ): JsonResponse|RedirectResponse {
        $updatedNotification = $this->notificationService->markAsRead($notification);

        if (! $request->expectsJson()) {
            return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
        }

        return $this->successResponse('Notifikasi ditandai sudah dibaca.', [
            'notification_id' => $updatedNotification->id,
            'is_read' => (bool) $updatedNotification->is_read,
            'read_at' => $updatedNotification->read_at?->toISOString(),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $updatedCount = $this->notificationService->markAllAsRead($request->user());

        if (! $request->expectsJson()) {
            return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
        }

        return $this->successResponse('Semua notifikasi ditandai sudah dibaca.', [
            'updated_count' => $updatedCount,
        ]);
    }
}
