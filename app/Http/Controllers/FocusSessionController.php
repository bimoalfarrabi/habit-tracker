<?php

namespace App\Http\Controllers;

use App\Http\Requests\FocusSession\StartFocusSessionRequest;
use App\Http\Requests\FocusSession\StopFocusSessionRequest;
use App\Http\Resources\FocusSessionResource;
use App\Models\FocusSession;
use App\Models\Habit;
use App\Services\FocusSessionService;
use App\Support\Concerns\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class FocusSessionController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected FocusSessionService $focusSessionService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $runningSession = FocusSession::query()
            ->where('user_id', $user->id)
            ->where('status', 'running')
            ->latest('id')
            ->first();

        $todaySummary = $this->focusSessionService->getTodaySummary($user);

        $sessions = FocusSession::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->paginate(15);

        $habits = Habit::query()
            ->where('user_id', $user->id)
            ->active()
            ->orderBy('title')
            ->get();

        return view('focus-sessions.index', [
            'runningSession' => $runningSession,
            'todaySummary' => $todaySummary,
            'sessions' => $sessions,
            'habits' => $habits,
        ]);
    }

    public function start(StartFocusSessionRequest $request): JsonResponse
    {
        $result = $this->focusSessionService->start($request->user(), $request->validated());

        return $this->successResponse(
            $result['reused_existing_session']
                ? 'Sesi fokus yang sedang berjalan ditemukan.'
                : 'Sesi fokus dimulai.',
            [
                'session' => new FocusSessionResource($result['session']),
                'reused_existing_session' => $result['reused_existing_session'],
            ]
        );
    }

    public function stop(StopFocusSessionRequest $request, FocusSession $focusSession): JsonResponse
    {
        try {
            $session = $this->focusSessionService->stop($request->user(), $focusSession, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage(), [], 422);
        }

        return $this->successResponse('Sesi fokus dihentikan.', [
            'session' => new FocusSessionResource($session),
        ]);
    }

    public function todaySummary(Request $request): JsonResponse
    {
        return $this->successResponse('Focus summary loaded.', $this->focusSessionService->getTodaySummary($request->user()));
    }
}
