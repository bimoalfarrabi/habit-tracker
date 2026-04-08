@props([
    'label',
    'value',
    'hint' => null,
])

<x-card variant="metric">
    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-mutedText">{{ $label }}</p>
    <p class="mt-2 text-3xl font-semibold text-ink">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-warmText">{{ $hint }}</p>
    @endif
</x-card>
