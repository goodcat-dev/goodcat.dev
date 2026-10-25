@php
    $dots = ['fill-mint', 'fill-cobalt', 'fill-tangerine', 'fill-grape'];
    $count = $paginator->total();
@endphp

<x-layouts.site title="Blog" active="blog">
    <section class="mx-auto max-w-7xl px-5 pt-12 pb-14 md:px-8 md:pt-18">
        <h1 class="text-[64px] leading-[0.95] font-extrabold tracking-[-0.05em] md:text-[120px]">Blog</h1>
        <p class="mt-6 font-mono text-[13px] text-mist">{{ $count }} {{ Str::plural('post', $count) }}</p>
    </section>

    @foreach ($years as $year => $posts)
        <section aria-labelledby="year-{{ $year }}" class="mx-auto max-w-7xl px-5 pb-16 md:px-8">
            <x-site.section-label id="year-{{ $year }}" class="mb-2">{{ $year }}</x-site.section-label>
            <div class="flex flex-col">
                @foreach ($posts as $post)
                    <x-site.post-row :post="$post" :dot="$dots[$loop->index % count($dots)]">
                        {{ $post->publishedAt->format('M j') }} · {{ $post->readingTime }} min
                    </x-site.post-row>
                @endforeach
            </div>
        </section>
    @endforeach

    <div class="mx-auto max-w-7xl px-5 pb-30 md:px-8">
        {{ $paginator->links() }}
    </div>
</x-layouts.site>
