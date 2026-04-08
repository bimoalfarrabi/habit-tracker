<?php

namespace App\Http\Controllers;

use App\Http\Requests\Habit\StoreHabitRequest;
use App\Http\Requests\Habit\UpdateHabitRequest;
use App\Models\Habit;
use App\Services\HabitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HabitController extends Controller
{
    public function __construct(
        protected HabitService $habitService
    ) {}

    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $today = now()->toDateString();

        $habits = Habit::query()
            ->where('user_id', $request->user()->id)
            ->when($filter === 'active', fn ($query) => $query->active())
            ->when($filter === 'archived', fn ($query) => $query->whereNotNull('archived_at'))
            ->with(['logs' => fn ($query) => $query->whereDate('log_date', $today)])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('habits.index', [
            'habits' => $habits,
            'filter' => $filter,
        ]);
    }

    public function create(): View
    {
        return view('habits.create');
    }

    public function store(StoreHabitRequest $request): RedirectResponse
    {
        $this->habitService->createForUser($request->user(), $request->validated());

        return redirect()
            ->route('habits.index')
            ->with('success', 'Habit berhasil dibuat.');
    }

    public function show(Habit $habit): View
    {
        abort_unless($habit->user_id === auth()->id(), 403);

        $habit->load(['logs' => fn ($query) => $query->latest('log_date')->limit(30)]);

        return view('habits.show', [
            'habit' => $habit,
        ]);
    }

    public function edit(Habit $habit): View
    {
        abort_unless($habit->user_id === auth()->id(), 403);

        return view('habits.edit', [
            'habit' => $habit,
        ]);
    }

    public function update(UpdateHabitRequest $request, Habit $habit): RedirectResponse
    {
        $this->habitService->updateForUser($request->user(), $habit, $request->validated());

        return redirect()
            ->route('habits.index')
            ->with('success', 'Habit berhasil diperbarui.');
    }

    public function destroy(Habit $habit): RedirectResponse
    {
        abort_unless($habit->user_id === auth()->id(), 403);

        $habit->delete();

        return redirect()
            ->route('habits.index')
            ->with('success', 'Habit berhasil dihapus.');
    }

    public function archive(Habit $habit): RedirectResponse
    {
        $this->habitService->archiveForUser(request()->user(), $habit);

        return redirect()
            ->route('habits.index')
            ->with('success', 'Habit berhasil diarsipkan.');
    }

    public function toggleActive(Habit $habit): RedirectResponse
    {
        $updatedHabit = $this->habitService->toggleActiveForUser(request()->user(), $habit);

        return redirect()
            ->route('habits.index')
            ->with('success', $updatedHabit->is_active
                ? 'Habit berhasil diaktifkan kembali.'
                : 'Habit berhasil dinonaktifkan.');
    }
}
