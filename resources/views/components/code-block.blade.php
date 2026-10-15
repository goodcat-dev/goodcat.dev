@props(['file' => null, 'lang' => null, 'dot' => 'bg-mist'])

<figure {{ $attributes->class('mt-7 overflow-hidden rounded-[28px] bg-ink-2 shadow-[inset_0_0_0_1px_var(--color-line)] squircle') }}>
    @if ($file)
        <figcaption class="flex h-12 items-center gap-2.5 border-b border-line px-5 font-mono text-[12.5px] text-mist">
            <span aria-hidden="true" class="size-2.5 rounded-[3px] {{ $dot }}"></span>
            {{ $file }}
            @if ($lang)
                <span class="ml-auto rounded-full bg-line px-[9px] py-[3px] text-[11px] text-fog">{{ $lang }}</span>
            @endif
        </figcaption>
    @endif
    <pre class="overflow-x-auto px-[26px] py-[22px] font-mono text-sm leading-[1.75] text-zinc-200">{{ $slot }}</pre>
</figure>
