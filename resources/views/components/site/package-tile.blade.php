@props(['slug', 'package'])

<x-package-scope
    as="a"
    :package="$package"
    href="{{ $package['url'] }}"
    :class="Arr::toCssClasses([
        'flex flex-col justify-between gap-6 rounded-[48px] p-7 text-pkg-ink transition-transform duration-250 squircle hover:-translate-y-1 hover:-rotate-[0.4deg] md:p-9',
        'min-h-[360px] flex-[2_1_560px] bg-[radial-gradient(circle_at_0_100%,var(--pkg-main)_50%,var(--pkg-light)_130%)]' => $package['featured'],
        'min-h-[300px] flex-[1_1_300px] bg-pkg' => ! $package['featured'],
    ])"
>
    <span @class(['flex flex-wrap items-start justify-between gap-4', 'flex-row-reverse' => $package['featured']])>
        <x-site.package-icon
            :package="$package"
            stroke-width="{{ $package['featured'] ? 1.5 : 1.6 }}"
            :class="Arr::toCssClasses(['size-14' => $package['featured'], 'size-12 text-pkg-light' => ! $package['featured']])"
        />
        @if ($package['badge'])
            <span class="rounded-full bg-pkg-ink/12 px-3 py-1.5 font-mono text-[13px]">{{ __($package['badge']) }}</span>
        @endif
    </span>
    <span>
        @if ($package['featured'])
            <span class="block text-[40px] leading-none font-extrabold tracking-[-0.04em] md:text-[56px]">{{ $package['name'] }}</span>
        @else
            <span class="block text-[34px] leading-[1.05] font-extrabold tracking-[-0.03em]">{!! Str::replaceFirst('-', '-<br>', e($package['name'])) !!}</span>
        @endif
        <span @class(['mt-3 block max-w-[520px] leading-normal', 'text-[19px]' => $package['featured'], 'text-[17px]' => ! $package['featured']])>{{ __($package['description']) }}</span>
        @if ($package['featured'])
            <code class="mt-5 inline-block rounded-[18px] bg-pkg-ink px-4 py-2.5 font-mono text-sm text-pkg-light squircle">composer require goodcat/{{ $slug }}</code>
        @endif
    </span>
</x-package-scope>
