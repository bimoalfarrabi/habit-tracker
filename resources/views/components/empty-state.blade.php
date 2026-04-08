@props([
    'title',
    'description',
])

<x-card class="py-10 text-center">
    <h3 class="text-2xl font-semibold text-ink">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-lg text-sm text-warmText">{{ $description }}</p>
    @if (isset($action))
        <div class="mt-4 flex justify-center">{{ $action }}</div>
    @endif
</x-card>
