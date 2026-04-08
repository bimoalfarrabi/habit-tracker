@props([
    'variant' => 'default',
])

@php
$class = match ($variant) {
    'metric' => 'metric-card',
    'dark' => 'rounded-hero border border-charcoal bg-charcoal p-6 text-[#f4efe7] shadow-whisper',
    default => 'card-soft p-5',
};
@endphp

<div {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</div>
