@extends('layouts.app')

@section('content')
    <x-page-header title="Edit Todo" subtitle="Perbarui detail todo agar tetap sesuai prioritasmu." />

    <x-card>
        <form method="POST" action="{{ route('todos.update', $todo) }}">
            @method('PUT')
            @include('todos._form', ['todo' => $todo])
        </form>
    </x-card>
@endsection
