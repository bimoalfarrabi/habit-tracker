@extends('layouts.app')

@section('content')
    <x-page-header title="Create Todo" subtitle="Tuliskan tugas penting untuk hari ini supaya tidak terlewat." />

    <x-card>
        <form method="POST" action="{{ route('todos.store') }}">
            @include('todos._form')
        </form>
    </x-card>
@endsection
