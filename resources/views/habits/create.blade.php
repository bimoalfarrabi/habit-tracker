@extends('layouts.app')

@section('content')
    <x-page-header title="Create Habit" subtitle="Mulai dari target kecil yang bisa kamu jaga setiap hari." />

    <x-card>
        <form method="POST" action="{{ route('habits.store') }}">
            @include('habits._form')
        </form>
    </x-card>
@endsection
