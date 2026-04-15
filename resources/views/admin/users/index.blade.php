@extends('layouts.app')

@section('content')
    <x-page-header title="Manajemen User" subtitle="CRUD user khusus role admin.">
        <x-slot name="actions">
            <a href="{{ route('admin.users.create') }}" class="btn-primary-warm">Tambah User</a>
        </x-slot>
    </x-page-header>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-borderCream text-sm">
                <thead>
                    <tr class="text-left text-mutedText">
                        <th class="px-3 py-2 font-semibold">Nama</th>
                        <th class="px-3 py-2 font-semibold">Email</th>
                        <th class="px-3 py-2 font-semibold">Role</th>
                        <th class="px-3 py-2 font-semibold">Dibuat</th>
                        <th class="px-3 py-2 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-borderCream">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-3 py-3 font-semibold text-ink">{{ $user->name }}</td>
                            <td class="px-3 py-3 text-warmText">{{ $user->email }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $user->isAdmin() ? 'bg-[#e7f2eb] text-[#1f6f45]' : 'bg-sand text-warmText' }}">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-warmText">{{ $user->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary-warm">Edit</a>

                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="danger">Hapus</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-warmText">Belum ada data user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </x-card>
@endsection
