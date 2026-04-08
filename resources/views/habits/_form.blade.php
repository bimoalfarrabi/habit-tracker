@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="title" class="text-sm font-medium text-warmText">Title</label>
        <input id="title" name="title" class="form-control" value="{{ old('title', $habit->title ?? '') }}" required>
        @error('title') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="text-sm font-medium text-warmText">Description</label>
        <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $habit->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="frequency" class="text-sm font-medium text-warmText">Frequency</label>
        <select id="frequency" name="frequency" class="form-control" required>
            @php $frequency = old('frequency', $habit->frequency ?? 'daily'); @endphp
            <option value="daily" @selected($frequency === 'daily')>Daily</option>
            <option value="weekly" @selected($frequency === 'weekly')>Weekly</option>
        </select>
        @error('frequency') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="target_count" class="text-sm font-medium text-warmText">Target Count</label>
        <input id="target_count" name="target_count" type="number" min="1" class="form-control" value="{{ old('target_count', $habit->target_count ?? 1) }}" required>
        @error('target_count') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="reminder_time" class="text-sm font-medium text-warmText">Reminder Time</label>
        <input id="reminder_time" name="reminder_time" type="time" class="form-control" value="{{ old('reminder_time', isset($habit) && $habit->reminder_time ? \Carbon\Carbon::parse($habit->reminder_time)->format('H:i') : '') }}">
        @error('reminder_time') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="color" class="text-sm font-medium text-warmText">Color (optional)</label>
        @php
            $colorValue = old('color', $habit->color ?? '#c96442');
            if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $colorValue)) {
                $colorValue = '#c96442';
            }
        @endphp
        <div class="mt-1 flex items-center gap-3 rounded-soft border border-borderCream bg-white px-3 py-2">
            <input
                id="color"
                name="color"
                type="color"
                class="h-9 w-12 cursor-pointer rounded border-0 bg-transparent p-0"
                value="{{ $colorValue }}"
                title="Pilih warna habit"
            >
            <span class="text-sm text-warmText">Pilih warna habit</span>
        </div>
        @error('color') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="icon" class="text-sm font-medium text-warmText">Icon (optional)</label>
        <input id="icon" name="icon" class="form-control" value="{{ old('icon', $habit->icon ?? '') }}">
        @error('icon') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        @php
            $isActive = (bool) old('is_active', $habit->is_active ?? true);
        @endphp
        <label for="is_active" class="text-sm font-medium text-warmText">Status</label>
        <label class="mt-1 flex w-full items-center justify-between rounded-soft border border-borderCream bg-white px-3 py-2.5">
            <div>
                <p class="text-sm font-medium text-warmText">Active habit</p>
                <p class="text-xs text-mutedText">Matikan jika habit ingin diarsipkan sementara.</p>
            </div>

            <input type="hidden" name="is_active" value="0">
            <span class="relative inline-flex h-6 w-11 items-center">
                <input
                    id="is_active"
                    name="is_active"
                    type="checkbox"
                    value="1"
                    @checked($isActive)
                    class="peer sr-only"
                >
                <span class="h-6 w-11 rounded-full bg-[#d8d4c8] transition peer-focus:ring-2 peer-focus:ring-focusBlue peer-focus:ring-offset-1 peer-checked:bg-terracotta"></span>
                <span class="pointer-events-none absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
            </span>
        </label>
        @error('is_active') <p class="mt-1 text-xs text-dangerWarm">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex items-center gap-2">
    <x-button type="submit">Save Habit</x-button>
    <a href="{{ route('habits.index') }}" class="btn-secondary-warm">Cancel</a>
</div>
