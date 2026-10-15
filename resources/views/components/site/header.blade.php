@props(['active' => null])

@php
    $items = [
        'blog' => ['label' => 'Blog', 'href' => route('home').'#blog'],
        'packages' => ['label' => 'Packages', 'href' => route('home').'#packages'],
        'about' => ['label' => 'About', 'href' => route('home').'#about'],
    ];
@endphp

<header class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-5 md:px-8">
    <x-site.logo />

    <div class="flex flex-wrap items-center gap-2">
        <nav aria-label="Main" class="flex flex-wrap gap-1.5 rounded-full bg-ink-3 p-1 text-[15px]">
            @foreach ($items as $key => $item)
                <a
                    href="{{ $item['href'] }}"
                    @if ($active === $key) aria-current="page" @endif
                    @class([
                        'rounded-full px-[18px] py-2.5',
                        'bg-chalk font-medium text-ink' => $active === $key,
                        'hover:text-acid' => $active !== $key,
                    ])
                >{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <x-locale.switcher align="responsive" />
    </div>
</header>
