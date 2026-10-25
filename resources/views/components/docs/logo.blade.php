@props(['package'])

<a href="{{ route('docs.index', $package['slug']) }}" aria-label="{{ $package['name'] }} documentation home" {{ $attributes->class('flex shrink-0 items-center gap-3 text-xl font-extrabold tracking-[-0.03em] hover:text-accent') }}>
    <x-site.blob class="size-9 text-white" shape="fill-[url(#docs-mark)]">
        <x-site.package-icon :package="$package" stroke-width="1.8" class="size-6" />
    </x-site.blob>
    {{ $package['name'] }}
</a>
