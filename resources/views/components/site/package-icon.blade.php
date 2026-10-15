@props(['package'])

<svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="{{ $package['icon'] }}" />
</svg>
