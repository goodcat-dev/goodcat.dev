@props(['post', 'dot' => 'fill-mint'])

<a href="{{ $post->url() }}" class="flex flex-wrap items-center gap-x-7 gap-y-4 border-b border-line py-7 hover:text-accent">
    <x-site.blob class="size-[18px]" :shape="$dot" />
    <span class="min-w-0 flex-[1_1_480px] text-2xl leading-[1.15] font-bold tracking-[-0.03em] md:text-[34px]">{{ $post->title }}</span>
    <span class="font-mono text-[13px] text-mist">{{ $slot }}</span>
</a>
