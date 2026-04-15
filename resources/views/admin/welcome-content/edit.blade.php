@extends('layouts.app')

@section('content')
    <x-page-header title="CMS Welcome Page" subtitle="Kelola konten utama halaman landing Ritme dari panel admin.">
        <x-slot name="actions">
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-secondary-warm">Preview Website</a>
        </x-slot>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.welcome-content.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <x-input-label for="hero_badge" value="Hero Badge" />
                    <input id="hero_badge" name="hero_badge" type="text" class="form-control" value="{{ old('hero_badge', $welcomeContent->hero_badge) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('hero_badge')" />
                </div>
                <div>
                    <x-input-label for="hero_highlight" value="Hero Highlight" />
                    <input id="hero_highlight" name="hero_highlight" type="text" class="form-control" value="{{ old('hero_highlight', $welcomeContent->hero_highlight) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('hero_highlight')" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="hero_title" value="Hero Title" />
                    <input id="hero_title" name="hero_title" type="text" class="form-control" value="{{ old('hero_title', $welcomeContent->hero_title) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('hero_title')" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="hero_description" value="Hero Description" />
                    <textarea id="hero_description" name="hero_description" rows="3" class="form-control" required>{{ old('hero_description', $welcomeContent->hero_description) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('hero_description')" />
                </div>
                <div>
                    <x-input-label for="hero_primary_cta_text" value="Hero Primary CTA" />
                    <input id="hero_primary_cta_text" name="hero_primary_cta_text" type="text" class="form-control" value="{{ old('hero_primary_cta_text', $welcomeContent->hero_primary_cta_text) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('hero_primary_cta_text')" />
                </div>
                <div>
                    <x-input-label for="hero_secondary_cta_text" value="Hero Secondary CTA" />
                    <input id="hero_secondary_cta_text" name="hero_secondary_cta_text" type="text" class="form-control" value="{{ old('hero_secondary_cta_text', $welcomeContent->hero_secondary_cta_text) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('hero_secondary_cta_text')" />
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <x-input-label for="preview_title" value="Preview Section Title" />
                    <input id="preview_title" name="preview_title" type="text" class="form-control" value="{{ old('preview_title', $welcomeContent->preview_title) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('preview_title')" />
                </div>
                <div>
                    <x-input-label for="features_title" value="Features Section Title" />
                    <input id="features_title" name="features_title" type="text" class="form-control" value="{{ old('features_title', $welcomeContent->features_title) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('features_title')" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="preview_description" value="Preview Description" />
                    <textarea id="preview_description" name="preview_description" rows="3" class="form-control" required>{{ old('preview_description', $welcomeContent->preview_description) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('preview_description')" />
                </div>
                <div>
                    <x-input-label for="stories_title" value="Stories Section Title" />
                    <input id="stories_title" name="stories_title" type="text" class="form-control" value="{{ old('stories_title', $welcomeContent->stories_title) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('stories_title')" />
                </div>
                <div>
                    <x-input-label for="how_it_works_title" value="How It Works Title" />
                    <input id="how_it_works_title" name="how_it_works_title" type="text" class="form-control" value="{{ old('how_it_works_title', $welcomeContent->how_it_works_title) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('how_it_works_title')" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="stories_description" value="Stories Description" />
                    <textarea id="stories_description" name="stories_description" rows="3" class="form-control" required>{{ old('stories_description', $welcomeContent->stories_description) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('stories_description')" />
                </div>
                <div>
                    <x-input-label for="final_cta_title" value="Final CTA Title" />
                    <input id="final_cta_title" name="final_cta_title" type="text" class="form-control" value="{{ old('final_cta_title', $welcomeContent->final_cta_title) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('final_cta_title')" />
                </div>
                <div>
                    <x-input-label for="footer_note" value="Footer Note" />
                    <input id="footer_note" name="footer_note" type="text" class="form-control" value="{{ old('footer_note', $welcomeContent->footer_note) }}" required>
                    <x-input-error class="mt-2" :messages="$errors->get('footer_note')" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="final_cta_description" value="Final CTA Description" />
                    <textarea id="final_cta_description" name="final_cta_description" rows="3" class="form-control" required>{{ old('final_cta_description', $welcomeContent->final_cta_description) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('final_cta_description')" />
                </div>
            </div>

            <div class="flex justify-end">
                <x-button type="submit">Simpan Konten Welcome</x-button>
            </div>
        </form>
    </x-card>
@endsection
