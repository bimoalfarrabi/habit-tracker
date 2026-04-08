<?php

namespace App\Http\Requests\Notification;

use App\Models\UserNotification;
use Illuminate\Foundation\Http\FormRequest;

class MarkNotificationAsReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $notification = $this->route('notification');

        return auth()->check()
            && $notification instanceof UserNotification
            && $notification->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [];
    }
}
