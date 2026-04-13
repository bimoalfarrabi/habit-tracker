<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNotificationChannelSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'email_notifications_enabled' => ['required', 'boolean'],
            'telegram_notifications_enabled' => ['required', 'boolean'],
            'telegram_chat_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf(fn (): bool => $this->boolean('telegram_notifications_enabled')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'telegram_chat_id.required' => 'Chat ID Telegram wajib diisi jika notifikasi Telegram diaktifkan.',
        ];
    }
}
