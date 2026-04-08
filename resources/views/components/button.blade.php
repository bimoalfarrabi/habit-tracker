@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
$variantClass = [
    'primary' => 'btn-primary-warm',
    'secondary' => 'btn-secondary-warm',
    'ghost' => 'btn-ghost-warm',
    'danger' => 'inline-flex items-center justify-center rounded-soft bg-dangerWarm px-4 py-2 text-sm font-semibold text-ivory shadow-ringWarm transition hover:opacity-90',
][$variant] ?? 'btn-primary-warm';
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $variantClass]) }}>
    {{ $slot }}
</button>
