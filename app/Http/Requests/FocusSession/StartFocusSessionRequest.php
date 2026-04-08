<?php

namespace App\Http\Requests\FocusSession;

use App\Models\Habit;
use Illuminate\Foundation\Http\FormRequest;

class StartFocusSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $habitId = $this->input('habit_id');

        if (! $habitId) {
            return true;
        }

        return Habit::query()
            ->where('id', $habitId)
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function rules(): array
    {
        return [
            'habit_id' => ['nullable', 'exists:habits,id'],
            'planned_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'note' => ['nullable', 'string'],
        ];
    }
}
