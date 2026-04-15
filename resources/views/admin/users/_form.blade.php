@php
    $editingUser = $targetUser ?? null;
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <x-input-label for="name" value="Nama" />
        <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $editingUser?->name) }}" required>
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" value="Email" />
        <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $editingUser?->email) }}" required>
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="role" value="Role" />
        <select id="role" name="role" class="form-control" required>
            <option value="{{ \App\Models\User::ROLE_USER }}" @selected(old('role', $editingUser?->role ?? \App\Models\User::ROLE_USER) === \App\Models\User::ROLE_USER)>User</option>
            <option value="{{ \App\Models\User::ROLE_ADMIN }}" @selected(old('role', $editingUser?->role) === \App\Models\User::ROLE_ADMIN)>Admin</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('role')" />
    </div>

    <div>
        <x-input-label for="password" :value="$editingUser ? 'Password Baru (opsional)' : 'Password'" />
        <input id="password" name="password" type="password" class="form-control" @required(! $editingUser)>
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="password_confirmation" value="Konfirmasi Password" />
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" @required(! $editingUser)>
    </div>
</div>

<div class="mt-6 flex justify-end">
    <x-button type="submit">{{ $submitLabel }}</x-button>
</div>
