<?php

namespace App\Http\Requests\HabitLog;

use App\Models\Habit;
use Illuminate\Foundation\Http\FormRequest;

class StoreHabitLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $habitId = $this->input('habit_id');

        if (! $habitId) {
            return false;
        }

        return Habit::query()
            ->where('id', $habitId)
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function rules(): array
    {
        $isQuickCheckin = $this->routeIs('ajax.habit-logs.quick-checkin');

        return [
            'habit_id' => ['required', 'exists:habits,id'],
            'log_date' => $isQuickCheckin ? ['nullable', 'date'] : ['required', 'date'],
            'status' => $isQuickCheckin
                ? ['nullable', 'in:completed,skipped,missed']
                : ['required', 'in:completed,skipped,missed'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'note' => ['nullable', 'string'],
        ];
    }
}
