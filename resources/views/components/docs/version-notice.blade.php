@props(['package', 'version', 'versions'])

@php
    $latest = $package['current_version'];
@endphp

@if ($version !== $latest && isset($versions[$latest]))
    <aside class="mb-10 flex flex-col gap-2 rounded-[28px] bg-tangerine/10 p-6 shadow-[inset_0_0_0_2px_var(--color-tangerine)] squircle sm:flex-row sm:items-center sm:gap-4">
        <p class="text-base text-fog">{{ __('You’re reading the documentation for version :version.', ['version' => $version]) }}</p>
        <a href="{{ $versions[$latest] }}" class="shrink-0 font-bold text-tangerine-text hover:text-accent sm:ml-auto">{{ __('Go to :version', ['version' => $latest]) }} →</a>
    </aside>
@endif
