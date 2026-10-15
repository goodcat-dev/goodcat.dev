@props(['file' => null, 'lang' => null, 'dot' => null, 'highlighted' => false, 'copyable' => false])

@php
    $dot ??= match ($lang) {
        'php' => 'bg-grape',
        'js', 'javascript' => 'bg-sun',
        'ts', 'typescript' => 'bg-cobalt',
        'vue' => 'bg-mint',
        'blade', 'html' => 'bg-tangerine',
        default => 'bg-mist',
    };
@endphp

<figure @if ($copyable) data-code x-data="copyCode" @endif {{ $attributes->class('group relative mt-7 overflow-hidden rounded-[28px] bg-ink-2 shadow-[inset_0_0_0_1px_var(--color-line)] squircle') }}>
    @if ($file)
        <figcaption class="flex h-12 items-center gap-2.5 border-b border-line pr-1 pl-5 font-mono text-[12.5px] text-mist">
            <span aria-hidden="true" class="size-2.5 shrink-0 rounded-[3px] {{ $dot }}"></span>
            <span class="truncate">{{ $file }}</span>
            @if ($lang)
                <span class="ml-auto rounded-full bg-line px-[9px] py-[3px] text-[11px] text-fog">{{ $lang }}</span>
            @endif
            @if ($copyable)
                <x-copy-button @class(['ml-auto' => ! $lang]) />
            @endif
        </figcaption>
    @elseif ($copyable)
        <x-copy-button class="absolute top-1.5 right-1.5 opacity-0 group-hover:opacity-100 focus-visible:opacity-100" />
    @endif
    @if ($highlighted)
        <div class="[&_pre]:overflow-x-auto [&_pre]:px-[26px] [&_pre]:py-[22px] [&_pre]:font-mono [&_pre]:text-sm [&_pre]:leading-[1.75]">{{ $slot }}</div>
    @else
        <pre class="overflow-x-auto px-[26px] py-[22px] font-mono text-sm leading-[1.75] text-zinc-200">{{ $slot }}</pre>
    @endif
</figure>
