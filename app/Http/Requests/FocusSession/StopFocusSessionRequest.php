<?php

namespace App\Http\Requests\FocusSession;

use App\Models\FocusSession;
use Illuminate\Foundation\Http\FormRequest;

class StopFocusSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $focusSession = $this->route('focusSession');

        return auth()->check()
            && $focusSession instanceof FocusSession
            && $focusSession->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'focused_duration_seconds' => ['required', 'integer', 'min:0'],
            'unfocused_duration_seconds' => ['required', 'integer', 'min:0'],
            'interruption_count' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:completed,cancelled'],
        ];
    }
}
