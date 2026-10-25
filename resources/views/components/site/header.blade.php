@props(['active' => null])

@php
    $items = [
        'blog' => ['label' => 'Blog', 'href' => route('posts.index')],
        'packages' => ['label' => 'Packages', 'href' => route('home').'#packages'],
        'about' => ['label' => 'About', 'href' => route('home').'#about'],
    ];
@endphp

<header x-data="disclosure" x-on:keydown.escape.window="open = false" class="mx-auto max-w-7xl px-5 py-5 [view-transition-name:site-header] md:px-8">
    <div class="flex items-center justify-between gap-4">
        <x-site.logo />

        <div class="flex items-center gap-2">
            <nav aria-label="Main" class="hidden gap-1.5 rounded-full bg-ink-3 p-1 text-[15px] md:flex">
                @foreach ($items as $key => $item)
                    <a
                        href="{{ $item['href'] }}"
                        @if ($active === $key) aria-current="page" @endif
                        @class([
                            'rounded-full px-[18px] py-2.5',
                            'bg-chalk font-medium text-ink' => $active === $key,
                            'hover:text-accent' => $active !== $key,
                        ])
                    >{{ $item['label'] }}</a>
                @endforeach
            </nav>
            <x-l10n::switcher class="hidden md:block" />
            <x-theme-toggle />
            <button type="button" x-on:click="toggle" x-bind:aria-expanded="open" x-bind:aria-label="open ? @js(__('Close navigation')) : @js(__('Open navigation'))" aria-label="{{ __('Open navigation') }}" aria-controls="site-mobile-nav" class="flex size-11 cursor-pointer items-center justify-center rounded-full bg-ink-3 text-chalk md:hidden">
                <svg x-show="! open" class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 6h14M3 10h14M3 14h14" /></svg>
                <svg x-show="open" x-cloak class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m5 5 10 10M15 5 5 15" /></svg>
            </button>
        </div>
    </div>

    <div id="site-mobile-nav" x-show="open" x-cloak x-on:click="$event.target.closest('a') && (open = false)" class="mt-5 flex flex-col gap-7 border-t border-line pt-6 pb-4 text-[17px] md:hidden">
        <nav aria-label="Main" class="flex flex-col gap-1">
            @foreach ($items as $key => $item)
                <a
                    href="{{ $item['href'] }}"
                    @if ($active === $key) aria-current="page" @endif
                    @class([
                        'rounded-[14px] px-3 py-2.5 squircle',
                        'bg-chalk font-bold text-ink' => $active === $key,
                        'text-fog hover:bg-ink-3 hover:text-chalk' => $active !== $key,
                    ])
                >{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <x-l10n::switcher variant="list" />
    </div>
</header>
