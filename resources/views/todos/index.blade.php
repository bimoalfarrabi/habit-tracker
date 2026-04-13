@extends('layouts.app')

@section('content')
    <x-page-header title="Todos" subtitle="Daftar tugas harian dengan pengingat terjadwal.">
        <x-slot:actions>
            <a href="{{ route('todos.create') }}" class="btn-primary-warm">+ Create Todo</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-5 flex flex-wrap gap-2">
        @php
            $filters = [
                'all' => 'All',
                'pending' => 'Pending',
                'completed' => 'Completed',
                'overdue' => 'Overdue',
            ];
        @endphp
        @foreach ($filters as $key => $label)
            <a href="{{ route('todos.index', ['filter' => $key]) }}" class="{{ $filter === $key ? 'btn-primary-warm' : 'btn-secondary-warm' }}">
                {{ $label }} ({{ $counts[$key] ?? 0 }})
            </a>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse ($todos as $todo)
            @php
                $priorityClass = match ($todo->priority) {
                    'high' => 'bg-[#ffd8d0] text-[#9b3b22]',
                    'low' => 'bg-[#dff3e6] text-[#2f6f46]',
                    default => 'bg-sand text-warmText',
                };
                $isOverdue = ! $todo->is_completed && $todo->due_date && $todo->due_date->isPast() && ! $todo->due_date->isToday();
            @endphp
            <x-card class="{{ $todo->is_completed ? 'opacity-80' : '' }}">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-xl text-ink {{ $todo->is_completed ? 'line-through text-mutedText' : '' }}">
                                {{ $todo->title }}
                            </h3>
                            <span class="badge-soft {{ $priorityClass }}">{{ ucfirst($todo->priority) }}</span>
                            @if ($isOverdue)
                                <span class="badge-soft bg-[#ffd8d0] text-[#9b3b22]">Overdue</span>
                            @endif
                        </div>

                        @if ($todo->description)
                            <p class="text-sm text-warmText">{{ $todo->description }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-3 text-xs text-mutedText">
                            @if ($todo->due_date)
                                <span>Due: {{ $todo->due_date->format('d M Y') }}</span>
                            @else
                                <span>No due date</span>
                            @endif

                            @if ($todo->reminder_time)
                                <span>Reminder: {{ \Carbon\Carbon::parse($todo->reminder_time)->format('H:i') }}</span>
                            @endif

                            @if ($todo->is_completed && $todo->completed_at)
                                <span>Completed: {{ $todo->completed_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('todos.toggle-completion', $todo) }}">
                            @csrf
                            <input type="hidden" name="is_completed" value="{{ $todo->is_completed ? 0 : 1 }}">
                            <button type="submit" class="{{ $todo->is_completed ? 'btn-secondary-warm' : 'btn-primary-warm' }}">
                                {{ $todo->is_completed ? 'Mark Pending' : 'Mark Done' }}
                            </button>
                        </form>

                        <a href="{{ route('todos.edit', $todo) }}" class="btn-ghost-warm">Edit</a>

                        <form method="POST" action="{{ route('todos.destroy', $todo) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost-warm text-dangerWarm">Delete</button>
                        </form>
                    </div>
                </div>
            </x-card>
        @empty
            <x-empty-state
                title="Belum ada todo"
                description="Tambahkan tugas pertama dan aktifkan reminder supaya tidak terlewat."
            >
                <x-slot:action>
                    <a href="{{ route('todos.create') }}" class="btn-primary-warm">Buat Todo</a>
                </x-slot:action>
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $todos->links() }}
    </div>
@endsection
