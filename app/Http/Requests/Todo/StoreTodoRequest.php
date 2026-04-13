<?php

namespace App\Http\Requests\Todo;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date', 'required_with:reminder_time'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'priority' => ['nullable', 'in:low,medium,high'],
        ];
    }
}
