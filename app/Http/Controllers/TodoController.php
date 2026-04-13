<?php

namespace App\Http\Controllers;

use App\Http\Requests\Todo\StoreTodoRequest;
use App\Http\Requests\Todo\ToggleTodoCompletionRequest;
use App\Http\Requests\Todo\UpdateTodoRequest;
use App\Models\Todo;
use App\Services\TodoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function __construct(
        protected TodoService $todoService
    ) {}

    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $today = now()->toDateString();

        $todos = Todo::query()
            ->where('user_id', $request->user()->id)
            ->when($filter === 'pending', fn ($query) => $query->pending())
            ->when($filter === 'completed', fn ($query) => $query->where('is_completed', true))
            ->when(
                $filter === 'overdue',
                fn ($query) => $query
                    ->pending()
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today)
            )
            ->orderBy('is_completed')
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'all' => Todo::query()->where('user_id', $request->user()->id)->count(),
            'pending' => Todo::query()->where('user_id', $request->user()->id)->pending()->count(),
            'completed' => Todo::query()->where('user_id', $request->user()->id)->where('is_completed', true)->count(),
            'overdue' => Todo::query()
                ->where('user_id', $request->user()->id)
                ->pending()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->count(),
        ];

        return view('todos.index', [
            'todos' => $todos,
            'filter' => $filter,
            'counts' => $counts,
        ]);
    }

    public function create(): View
    {
        return view('todos.create');
    }

    public function store(StoreTodoRequest $request): RedirectResponse
    {
        $this->todoService->createForUser($request->user(), $request->validated());

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todo berhasil dibuat.');
    }

    public function edit(Todo $todo): View
    {
        abort_unless($todo->user_id === auth()->id(), 403);

        return view('todos.edit', [
            'todo' => $todo,
        ]);
    }

    public function update(UpdateTodoRequest $request, Todo $todo): RedirectResponse
    {
        $this->todoService->updateForUser($request->user(), $todo, $request->validated());

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todo berhasil diperbarui.');
    }

    public function destroy(Todo $todo): RedirectResponse
    {
        $this->todoService->deleteForUser(request()->user(), $todo);

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todo berhasil dihapus.');
    }

    public function toggleCompletion(ToggleTodoCompletionRequest $request, Todo $todo): RedirectResponse
    {
        $updatedTodo = $this->todoService->setCompletionForUser(
            $request->user(),
            $todo,
            (bool) $request->boolean('is_completed'),
        );

        return back()->with(
            'success',
            $updatedTodo->is_completed
                ? 'Todo ditandai selesai.'
                : 'Todo dikembalikan ke pending.',
        );
    }
}
