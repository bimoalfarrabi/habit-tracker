<?php

namespace App\Http\Requests\Todo;

use App\Models\Todo;
use Illuminate\Foundation\Http\FormRequest;

class ToggleTodoCompletionRequest extends FormRequest
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
            'is_completed' => ['required', 'boolean'],
        ];
    }
}
