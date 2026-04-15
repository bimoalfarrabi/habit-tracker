<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWelcomePageContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'hero_badge' => ['required', 'string', 'max:255'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_highlight' => ['required', 'string', 'max:255'],
            'hero_description' => ['required', 'string'],
            'hero_primary_cta_text' => ['required', 'string', 'max:120'],
            'hero_secondary_cta_text' => ['required', 'string', 'max:120'],
            'preview_title' => ['required', 'string', 'max:255'],
            'preview_description' => ['required', 'string'],
            'stories_title' => ['required', 'string', 'max:255'],
            'stories_description' => ['required', 'string'],
            'features_title' => ['required', 'string', 'max:255'],
            'how_it_works_title' => ['required', 'string', 'max:255'],
            'final_cta_title' => ['required', 'string', 'max:255'],
            'final_cta_description' => ['required', 'string'],
            'footer_note' => ['required', 'string', 'max:255'],
        ];
    }
}
