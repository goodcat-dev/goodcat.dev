@props(['strokeWidth' => 4.5])

<svg {{ $attributes }} viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="{{ $strokeWidth }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M10 40C10 30 11 22 13.5 9L26 16.5C30 15.6 34 15.6 38 16.5L50.5 9C53 22 54 30 54 40C54 50.5 44 56.5 32 56.5S10 50.5 10 40Z" />
    <path d="M19 29.5Q23.5 34 28 29.5M36 29.5Q40.5 34 45 29.5" />
    <path d="M29.2 36.4h5.6L32 39.4z" fill="currentColor" stroke-width="2" />
    <path d="M32 39.4V41M26 42Q29 45 32 41Q36.5 45.5 40.5 38.5" stroke-width="{{ $strokeWidth - 0.5 }}" />
    <path d="M2 34.5H12M3.5 42H12M52 34.5H62M52 42H60.5" />
</svg>
