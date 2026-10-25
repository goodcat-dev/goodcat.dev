@php
    $isIntroduction = $page->slug === 'introduction';
    $guides = $page->frontMatter['guides'] ?? [];
    $features = $page->frontMatter['features'] ?? [];
    $toc = $page->sections + array_filter(['guides' => $guides ? __('Guides') : null, 'features' => $features ? __('Features') : null]);
    $pageUrl = fn (string $slug) => route('docs.show', [$package['slug'], $version, $slug]);
    $arrow = '<svg class="size-[18px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 10h10M11 6l4 4-4 4" /></svg>';
    $featureStyles = [
        ['card' => 'bg-pkg text-pkg-ink hover:text-pkg-ink', 'icon' => '', 'text' => '', 'path' => 'M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205 12 12m6.894 5.785-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495'],
        ['card' => 'bg-ink-2 shadow-[inset_0_0_0_2px_var(--pkg-main)] hover:text-chalk', 'icon' => 'text-pkg-text', 'text' => 'text-fog', 'path' => 'M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z'],
        ['card' => 'bg-pkg-light text-black hover:text-black light:shadow-[inset_0_0_0_2px_var(--pkg-main)]', 'icon' => '', 'text' => '', 'path' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
    ];
@endphp

<x-layouts.docs :package="$package" :version="$version" :current="$page->slug" :navigation="$navigation" :title="$isIntroduction ? null : $page->title" :toc="$toc">
    @if ($isIntroduction)
        <section class="rounded-[48px] bg-[radial-gradient(circle_at_0_100%,var(--pkg-main)_50%,var(--pkg-light)_130%)] px-7 pt-12 pb-10 text-pkg-ink squircle md:px-12 md:pt-14 md:pb-12">
            <p class="font-mono text-[13px] break-all">composer require goodcat/{{ $package['slug'] }}</p>
            <h1 class="mt-[18px] text-[56px] leading-[0.95] font-extrabold tracking-[-0.05em] md:text-[88px]">{{ $page->title }}</h1>
            @if ($page->description)
                <p class="mt-[22px] max-w-[640px] text-[21px] leading-normal">{{ $page->description }}</p>
            @endif
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ $pageUrl('quickstart') }}" class="inline-flex h-[50px] items-center gap-2.5 rounded-full bg-black px-6 text-[17px] font-bold text-zinc-100 hover:text-zinc-100">{{ $titles['quickstart'] ?? 'Quickstart' }} {!! $arrow !!}</a>
                <a href="{{ $package['repository'] }}" class="inline-flex h-[50px] items-center rounded-full px-6 text-[17px] font-bold text-pkg-ink shadow-[inset_0_0_0_2px_var(--pkg-ink)] hover:text-pkg-ink">{{ __('View on GitHub') }}</a>
            </div>
        </section>
    @else
        <header>
            @if ($section)
                <p class="font-mono text-[13px] text-pkg-text">// {{ $section }}</p>
            @endif
            <h1 class="mt-3 text-5xl leading-[0.95] font-extrabold tracking-[-0.05em] md:text-[72px]">{{ $page->title }}</h1>
            @if ($page->description)
                <p class="mt-5 max-w-[680px] text-[21px] leading-normal text-zinc-200 light:text-zinc-800">{{ $page->description }}</p>
            @endif
        </header>
    @endif

    <div @class(['markdown', 'mt-18' => $isIntroduction, 'mt-14' => ! $isIntroduction])>
        {!! $page->html !!}
    </div>

    @if ($isIntroduction)
        <a href="{{ $pageUrl('quickstart') }}" class="mt-5 inline-flex min-h-11 items-center gap-2 text-[17px] font-bold text-pkg-text hover:text-accent">{{ __('Get started with the Quickstart') }} {!! $arrow !!}</a>
    @endif

    @if ($guides)
        <h2 id="guides" class="mt-18 scroll-mt-24 text-[32px] font-extrabold tracking-[-0.035em] md:text-[40px]">{{ __('Guides') }}</h2>
        <div class="mt-6 grid gap-3.5 sm:grid-cols-2">
            @foreach ($guides as $guide)
                <a href="{{ $pageUrl($guide['page']) }}" class="flex flex-col gap-2.5 rounded-[32px] bg-ink-2 p-7 light:shadow-[inset_0_0_0_1px_var(--color-line)] transition-transform duration-250 squircle hover:-translate-y-[3px] hover:text-chalk">
                    <span class="font-mono text-xs text-pkg-text">{{ sprintf('%02d', $loop->iteration) }}</span>
                    <span class="text-[22px] font-extrabold tracking-[-0.02em]">{{ $guide['title'] ?? $titles[$guide['page']] ?? $guide['page'] }}</span>
                    <span class="text-base leading-[1.55] text-mist">{{ $guide['description'] }}</span>
                    <span class="mt-auto pt-2 font-bold text-pkg-text">{{ __('Read more') }} →</span>
                </a>
            @endforeach
        </div>
    @endif

    @if ($features)
        <h2 id="features" class="mt-18 scroll-mt-24 text-[32px] font-extrabold tracking-[-0.035em] md:text-[40px]">{{ __('Features') }}</h2>
        <div class="mt-6 flex flex-wrap gap-3.5">
            @foreach ($features as $feature)
                @php($style = $featureStyles[$loop->index % count($featureStyles)])
                <a href="{{ $pageUrl($feature['page']) }}" class="flex min-h-60 flex-[1_1_240px] flex-col gap-3 rounded-[32px] p-7 transition-transform duration-250 squircle hover:-translate-y-[3px] {{ $style['card'] }}">
                    <svg class="size-10 {{ $style['icon'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $style['path'] }}" /></svg>
                    <span class="mt-auto text-[22px] font-extrabold tracking-[-0.02em]">{{ $feature['title'] ?? $titles[$feature['page']] ?? $feature['page'] }}</span>
                    <span class="text-base leading-normal {{ $style['text'] }}">{{ $feature['description'] }}</span>
                </a>
            @endforeach
        </div>
    @endif

    <div x-data="feedback" class="mt-18 flex min-h-11 flex-wrap items-center gap-4">
        <div x-show="! voted" class="flex flex-wrap items-center gap-4">
            <span class="text-base text-fog">{{ __('Was this page helpful?') }}</span>
            <span class="flex gap-2">
                <button type="button" x-on:click="vote" class="h-11 cursor-pointer rounded-full bg-ink-3 px-5 font-bold hover:text-accent">{{ __('Yes') }}</button>
                <button type="button" x-on:click="vote" class="h-11 cursor-pointer rounded-full bg-ink-3 px-5 font-bold hover:text-accent">{{ __('No') }}</button>
            </span>
        </div>
        <span x-show="voted" x-cloak class="inline-flex items-center gap-2.5 text-base text-fog" role="status">
            <x-site.cat-mark :stroke-width="4" class="size-6 text-pkg-text" />
            {{ __('Thanks for your feedback!') }}
        </span>
    </div>

    @if ($next)
        <a href="{{ $pageUrl($next) }}" class="mt-7 flex items-center justify-between gap-4 rounded-[32px] bg-ink-2 px-8 py-[26px] light:shadow-[inset_0_0_0_1px_var(--color-line)] transition-transform duration-250 squircle hover:-translate-y-[3px] hover:text-chalk">
            <span>
                <span class="block font-mono text-xs text-mist">{{ __('next') }}</span>
                <span class="mt-1 block text-[26px] font-extrabold tracking-[-0.03em]">{{ $titles[$next] }}</span>
            </span>
            <x-site.blob class="size-13 text-pkg-ink" shape="fill-pkg">
                <svg class="size-[22px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 10h10M11 6l4 4-4 4" /></svg>
            </x-site.blob>
        </a>
    @endif
</x-layouts.docs>
