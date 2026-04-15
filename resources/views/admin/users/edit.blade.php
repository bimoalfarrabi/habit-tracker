@extends('layouts.app')

@section('content')
    <x-page-header title="Edit User" subtitle="Perbarui data user dan role dari panel admin.">
        <x-slot name="actions">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary-warm">Kembali</a>
        </x-slot>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.users.update', $targetUser) }}">
            @csrf
            @method('PUT')
            @include('admin.users._form', [
                'targetUser' => $targetUser,
                'submitLabel' => 'Perbarui User',
            ])
        </form>
    </x-card>
@endsection
