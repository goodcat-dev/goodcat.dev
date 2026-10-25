@props(['post', 'package' => null, 'previous' => null, 'next' => null])

@php
    $scope = $package ?? ['colors' => []];
@endphp

<x-layouts.site :title="$post->title" active="blog" compact-footer>
    <x-package-scope :package="$scope">
        <section class="mx-auto max-w-7xl px-5 pt-12 pb-14 md:px-8 md:pt-18">
            <div class="flex flex-wrap items-center gap-2.5">
                @if ($package)
                    <a href="{{ $package['url'] }}" class="inline-flex -rotate-2 items-center gap-2 rounded-2xl bg-pkg px-4 py-2 text-[15px] font-bold text-pkg-ink squircle hover:text-pkg-ink">{{ $package['name'] }}</a>
                @endif
                @foreach ($post->tags as $tag)
                    <span class="rounded-full bg-ink-3 px-3 py-1.5 font-mono text-[13px] text-mist">{{ $tag }}</span>
                @endforeach
            </div>

            <h1 class="mt-8 max-w-[1080px] text-5xl leading-[0.98] font-extrabold tracking-[-0.05em] md:text-[92px]">
                @foreach (explode('`', $post->headline) as $part)
                    @if ($loop->odd)
                        {{ $part }}
                    @else
                        <span class="inline-block -rotate-2 rounded-[32px] bg-acid px-[22px] pb-1.5 font-mono text-[0.8em] font-medium tracking-[-0.04em] text-black squircle">{{ $part }}</span>
                    @endif
                @endforeach
            </h1>

            <div class="mt-9 flex flex-wrap items-center gap-3.5 font-mono text-[13px] text-mist">
                <x-site.blob class="size-9 text-on-brand">
                    <x-site.cat-mark :stroke-width="4" class="size-7" />
                </x-site.blob>
                <span class="text-chalk">by Matteo</span>
                <span aria-hidden="true">·</span>
                <time datetime="{{ $post->publishedAt->toDateString() }}">{{ $post->publishedAt->format('M j, Y') }}</time>
                <span aria-hidden="true">·</span>
                <span>{{ $post->readingTime }} min read</span>
            </div>
        </section>

        <article class="mx-auto max-w-7xl px-5 pb-24 md:px-8">
            <div class="article max-w-[820px]">
                {{ $slot }}
            </div>
        </article>

        @if ($package)
            <section aria-label="Featured package" class="mx-auto max-w-7xl px-5 pb-24 md:px-8">
                <div class="flex flex-wrap items-end justify-between gap-8 rounded-[48px] bg-[radial-gradient(circle_at_0_100%,var(--pkg-main)_50%,var(--pkg-light)_130%)] p-8 text-pkg-ink squircle md:p-11">
                    <div class="flex-[1_1_480px]">
                        <span class="rounded-full bg-pkg-ink/12 px-3 py-1.5 font-mono text-[13px]">this article runs on</span>
                        <div class="mt-5 text-5xl leading-none font-extrabold tracking-[-0.045em] md:text-[64px]">{{ $package['name'] }}</div>
                        <p class="mt-3.5 max-w-[560px] text-[19px] leading-normal">{{ $package['description'] }}</p>
                        <code class="mt-5 inline-block rounded-[18px] bg-pkg-ink px-4 py-2.5 font-mono text-sm text-pkg-light squircle">composer require goodcat/{{ $package['slug'] }}</code>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ $package['url'] }}" class="inline-flex h-13 items-center rounded-full bg-pkg-ink px-[26px] text-[17px] font-bold text-pkg-light hover:text-pkg-light">Read the docs →</a>
                        <a href="{{ $package['repository'] }}" class="inline-flex h-13 items-center rounded-full px-[26px] text-[17px] font-bold text-pkg-ink shadow-[inset_0_0_0_2px_var(--pkg-ink)] hover:text-pkg-ink">GitHub</a>
                    </div>
                </div>
            </section>
        @endif

        @if ($previous || $next)
            <section aria-labelledby="keep-reading" class="mx-auto max-w-7xl px-5 pb-30 md:px-8">
                <x-site.section-label id="keep-reading" class="mb-2">keep reading</x-site.section-label>
                @if ($previous)
                    <x-site.post-row :post="$previous" dot="fill-mint">← previous</x-site.post-row>
                @endif
                @if ($next)
                    <x-site.post-row :post="$next" dot="fill-grape">next →</x-site.post-row>
                @endif
            </section>
        @endif
    </x-package-scope>
</x-layouts.site>
