<?php

namespace App\Http\Requests\HabitLog;

use App\Models\HabitLog;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHabitLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $habitLog = $this->route('habitLog');

        return auth()->check()
            && $habitLog instanceof HabitLog
            && $habitLog->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:completed,skipped,missed'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'note' => ['nullable', 'string'],
        ];
    }
}
