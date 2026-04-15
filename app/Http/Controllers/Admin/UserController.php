<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->latest('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::query()->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User baru berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'targetUser' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $isAdminDowngrade = $user->isAdmin() && ($data['role'] ?? User::ROLE_USER) !== User::ROLE_ADMIN;
        $remainingAdminCount = User::query()->where('role', User::ROLE_ADMIN)->count();

        if ($isAdminDowngrade && $remainingAdminCount <= 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Minimal harus ada satu akun admin aktif.');
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Akun admin yang sedang login tidak bisa dihapus.');
        }

        $remainingAdminCount = User::query()->where('role', User::ROLE_ADMIN)->count();

        if ($user->isAdmin() && $remainingAdminCount <= 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Minimal harus ada satu akun admin aktif.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
