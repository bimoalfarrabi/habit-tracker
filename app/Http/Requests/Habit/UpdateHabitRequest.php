<?php

namespace App\Http\Requests\Habit;

use App\Models\Habit;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $habit = $this->route('habit');

        return auth()->check()
            && $habit instanceof Habit
            && $habit->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency' => ['required', 'in:daily,weekly'],
            'target_count' => ['required', 'integer', 'min:1', 'max:1000'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
