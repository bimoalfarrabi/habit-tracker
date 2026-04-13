@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="title" class="text-sm font-medium text-warmText">Title</label>
        <input id="title" name="title" class="form-control" value="{{ old('title', $todo->title ?? '') }}" required>
        @error('title') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="text-sm font-medium text-warmText">Description</label>
        <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $todo->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="due_date" class="text-sm font-medium text-warmText">Due Date</label>
        <input
            id="due_date"
            name="due_date"
            type="date"
            class="form-control"
            value="{{ old('due_date', isset($todo) && $todo->due_date ? $todo->due_date->format('Y-m-d') : '') }}"
        >
        @error('due_date') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="reminder_time" class="text-sm font-medium text-warmText">Reminder Time</label>
        <input
            id="reminder_time"
            name="reminder_time"
            type="time"
            class="form-control"
            value="{{ old('reminder_time', isset($todo) && $todo->reminder_time ? \Carbon\Carbon::parse($todo->reminder_time)->format('H:i') : '') }}"
        >
        <p class="mt-1 text-xs text-mutedText">Reminder akan dikirim pada tanggal due dan jam yang dipilih.</p>
        @error('reminder_time') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="priority" class="text-sm font-medium text-warmText">Priority</label>
        @php
            $priority = old('priority', $todo->priority ?? 'medium');
        @endphp
        <select id="priority" name="priority" class="form-control">
            <option value="low" @selected($priority === 'low')>Low</option>
            <option value="medium" @selected($priority === 'medium')>Medium</option>
            <option value="high" @selected($priority === 'high')>High</option>
        </select>
        @error('priority') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex items-center gap-2">
    <x-button type="submit">Save Todo</x-button>
    <a href="{{ route('todos.index') }}" class="btn-secondary-warm">Cancel</a>
</div>
