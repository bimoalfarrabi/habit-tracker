<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWelcomePageContentRequest;
use App\Models\WelcomePageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WelcomePageContentController extends Controller
{
    public function edit(): View
    {
        return view('admin.welcome-content.edit', [
            'welcomeContent' => WelcomePageContent::singleton(),
        ]);
    }

    public function update(UpdateWelcomePageContentRequest $request): RedirectResponse
    {
        $welcomeContent = WelcomePageContent::singleton();
        $welcomeContent->fill($request->validated());
        $welcomeContent->save();

        return redirect()
            ->route('admin.welcome-content.edit')
            ->with('success', 'Konten halaman welcome berhasil diperbarui.');
    }
}
