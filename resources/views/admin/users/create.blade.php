@extends('layouts.app')

@section('content')
    <x-page-header title="Tambah User" subtitle="Buat akun user baru dari panel admin.">
        <x-slot name="actions">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary-warm">Kembali</a>
        </x-slot>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users._form', ['submitLabel' => 'Simpan User'])
        </form>
    </x-card>
@endsection
