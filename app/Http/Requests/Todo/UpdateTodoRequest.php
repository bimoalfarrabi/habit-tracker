<?php

namespace App\Http\Requests\Todo;

use App\Models\Todo;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $todo = $this->route('todo');

        return auth()->check()
            && $todo instanceof Todo
            && $todo->user_id === auth()->id();
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
