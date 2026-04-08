@props([
    'title',
    'subtitle' => null,
])

<div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
    <div>
        <h1 class="page-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endif
</div>
