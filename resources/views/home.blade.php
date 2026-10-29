@php
    $stickers = [
        'Design' => '-rotate-3 rounded-full bg-grape text-grape-ink',
        'Code' => 'squircle rotate-2 rounded-[42px] bg-mint text-mint-ink',
        'Repeat' => '-rotate-[1.5deg] rounded-[999px_999px_999px_24px] bg-tangerine text-tangerine-ink',
    ];
    $dots = ['fill-mint', 'fill-cobalt', 'fill-tangerine', 'fill-grape'];
@endphp

<x-layouts.site>
    <section class="mx-auto max-w-7xl px-5 pt-16 pb-20 md:px-8 md:pt-24">
        <h1 class="flex flex-wrap items-center gap-x-5 gap-y-3 text-[48px] leading-[0.95] font-extrabold tracking-[-0.05em] md:text-[120px]">
            @foreach ($stickers as $word => $classes)
                <span class="inline-block px-8 pt-1 pb-3.5 {{ $classes }}">{{ $word }}</span>
            @endforeach
        </h1>
        <div class="mt-12 flex flex-wrap items-end justify-between gap-8">
            <p class="max-w-[560px] text-[22px] leading-normal text-fog">
                Hi, I’m Matteo. I build things with Laravel, write about it here, and give the useful bits away as open source packages.
            </p>
            <a href="#blog" class="inline-flex h-13 items-center gap-2.5 rounded-full bg-brand px-[26px] text-[17px] font-bold text-on-brand">
                Start reading
                <svg class="size-[18px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 10h10M11 6l4 4-4 4" /></svg>
            </a>
        </div>
    </section>

    <section id="packages" aria-labelledby="packages-heading" class="mx-auto max-w-7xl scroll-mt-6 px-5 pb-24 md:px-8">
        <x-site.section-label id="packages-heading" class="mb-6">packages</x-site.section-label>
        <div class="flex flex-wrap gap-4">
            @foreach ($packages as $slug => $package)
                <x-site.package-tile :slug="$slug" :package="$package" />
            @endforeach
            <div id="about" class="flex min-h-[300px] flex-[1_1_300px] scroll-mt-6 flex-col justify-between gap-6 rounded-[48px] bg-ink-3 p-7 squircle md:p-9">
                <span class="font-mono text-[13px] text-mist">// about</span>
                <p class="text-[22px] leading-[1.4]">[A two-line bio: where you work, what you care about, where to find you.]</p>
            </div>
        </div>
    </section>

    <section id="blog" aria-labelledby="blog-heading" class="mx-auto max-w-7xl scroll-mt-6 px-5 pb-30 md:px-8">
        <div class="mb-2 flex flex-wrap items-baseline justify-between gap-4">
            <x-site.section-label id="blog-heading">fresh from the blog</x-site.section-label>
            <a href="{{ route('posts.index') }}" class="font-bold text-accent hover:text-chalk">All posts →</a>
        </div>
        <div class="flex flex-col">
            @foreach ($posts as $post)
                <x-site.post-row :post="$post" :dot="$dots[$loop->index % count($dots)]">
                    {{ $post->published_at->format('M j, Y') }} · {{ $post->reading_time }} min
                </x-site.post-row>
            @endforeach
        </div>
    </section>
</x-layouts.site>
