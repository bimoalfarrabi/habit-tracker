@extends('layouts.app')

@section('content')
    <x-page-header title="Edit Habit" subtitle="Perbarui detail habit agar tetap relevan dengan ritme harianmu." />

    <x-card>
        <form method="POST" action="{{ route('habits.update', $habit) }}">
            @method('PUT')
            @include('habits._form', ['habit' => $habit])
        </form>
    </x-card>
@endsection
