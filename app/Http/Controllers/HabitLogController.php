<?php

namespace App\Http\Controllers;

use App\Http\Requests\HabitLog\StoreHabitLogRequest;
use App\Http\Requests\HabitLog\UpdateHabitLogRequest;
use App\Http\Resources\HabitLogResource;
use App\Models\HabitLog;
use App\Services\HabitLogService;
use App\Support\Concerns\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class HabitLogController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected HabitLogService $habitLogService
    ) {}

    public function store(StoreHabitLogRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! isset($data['log_date'])) {
            $data['log_date'] = now()->toDateString();
        }

        $this->habitLogService->storeForUser($request->user(), $data);

        return back()->with('success', 'Log habit berhasil disimpan.');
    }

    public function update(UpdateHabitLogRequest $request, HabitLog $habitLog): RedirectResponse
    {
        $this->habitLogService->updateForUser($request->user(), $habitLog, $request->validated());

        return back()->with('success', 'Log habit berhasil diperbarui.');
    }

    public function quickCheckin(StoreHabitLogRequest $request): JsonResponse
    {
        $habitLog = $this->habitLogService->storeForUser(
            $request->user(),
            $request->validated(),
            forceToday: true,
        );

        $action = $habitLog->getAttribute('action') === 'created' ? 'created' : 'updated';

        return $this->successResponse(
            $action === 'created'
                ? 'Log habit berhasil dibuat.'
                : 'Log habit berhasil diperbarui.',
            [
                'action' => $action,
                'log' => new HabitLogResource($habitLog),
            ]
        );
    }
}
