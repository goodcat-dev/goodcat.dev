@props(['shape' => 'fill-brand'])

<span {{ $attributes->class('relative isolate flex shrink-0 items-center justify-center') }} aria-hidden="true">
    <svg viewBox="0 0 24 24" class="absolute inset-0 -z-10 size-full">
        <path class="{{ $shape }}" d="M12 0C2.4 0 0 2.4 0 12s2.4 12 12 12 12-2.4 12-12S21.6 0 12 0Z" />
    </svg>
    {{ $slot }}
</span>
