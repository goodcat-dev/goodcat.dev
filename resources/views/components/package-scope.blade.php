@props(['package', 'as' => 'div'])

<{{ $as }} {{ $attributes->merge([
    'style' => collect($package['colors'])
        ->map(fn (string $color, string $role) => "--pkg-{$role}: var(--color-{$color})")
        ->implode('; '),
]) }}>
    {{ $slot }}
</{{ $as }}>
