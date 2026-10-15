@props(['package', 'version', 'locale' => 'en'])

@php
    $config = [
        'appId' => config('services.algolia.app_id'),
        'searchKey' => config('services.algolia.search_key'),
        'index' => config('services.algolia.index'),
        'enabled' => filled(config('services.algolia.app_id')) && filled(config('services.algolia.search_key')),
        'filters' => "type:docs AND package:\"{$package['slug']}\" AND version:\"{$version}\" AND lang:{$locale}",
    ];
    $searchIcon = '<svg class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>';
@endphp

<div x-data="docsSearch(@js($config))" x-on:keydown.window="shortcut" {{ $attributes }}>
    <button type="button" x-on:click="open" class="hidden h-11 w-full cursor-pointer items-center gap-2.5 rounded-full bg-ink-3 pr-2 pl-4 shadow-[inset_0_0_0_1px_var(--color-line)] text-[15px] text-mist hover:text-fog md:flex">
        {!! $searchIcon !!}
        Search the docs
        <kbd class="ml-auto rounded-full bg-line px-2.5 py-1 font-mono text-xs text-fog" x-text="isMac ? '⌘ K' : 'Ctrl K'">Ctrl K</kbd>
    </button>
    <button type="button" x-on:click="open" aria-label="Search the docs" class="flex size-11 cursor-pointer items-center justify-center rounded-full bg-ink-3 text-fog md:hidden">
        {!! $searchIcon !!}
    </button>

    <dialog x-ref="dialog" aria-label="Search the docs" x-on:click.self="close" class="m-auto mt-[12vh] w-[calc(100%-2rem)] max-w-[640px] overflow-hidden rounded-[32px] bg-ink-2 p-0 text-chalk shadow-[inset_0_0_0_1px_var(--color-line),0_30px_80px_-20px_rgba(0,0,0,0.8)] squircle backdrop:bg-ink/75 backdrop:backdrop-blur-sm">
        <div class="flex items-center gap-3 border-b border-line px-5">
            <span class="text-mist">{!! $searchIcon !!}</span>
            <input
                x-ref="input"
                x-model="query"
                x-on:input.debounce.200ms="search"
                x-on:keydown.down.prevent="move(1)"
                x-on:keydown.up.prevent="move(-1)"
                x-on:keydown.enter.prevent="go"
                type="search"
                placeholder="Search {{ $package['name'] }} {{ $version }}"
                aria-label="Search query"
                autocomplete="off"
                class="h-16 min-w-0 flex-1 bg-transparent text-lg text-chalk placeholder:text-mist focus:outline-none"
            >
            <button type="button" x-on:click="close" class="cursor-pointer rounded-full bg-line px-2.5 py-1 font-mono text-xs text-fog">Esc</button>
        </div>

        <ul x-ref="results" role="listbox" aria-label="Results" class="max-h-[60vh] overflow-y-auto p-2 [&_mark]:bg-transparent [&_mark]:text-pkg-text">
            <template x-for="(hit, index) in hits" x-bind:key="hit.url">
                <li role="option" x-bind:aria-selected="active === index">
                    <a
                        x-bind:href="hit.url"
                        x-on:mouseenter="active = index"
                        x-on:click="close"
                        x-bind:class="active === index ? 'bg-ink-4' : ''"
                        class="flex flex-col gap-1 rounded-[20px] px-4 py-3 squircle hover:text-chalk"
                    >
                        <span x-show="hit.path" x-text="hit.path" class="font-mono text-xs text-mist"></span>
                        <span x-html="hit.title" class="font-bold"></span>
                        <span x-show="hit.snippet" x-html="hit.snippet" class="line-clamp-2 text-sm leading-relaxed text-fog"></span>
                    </a>
                </li>
            </template>
        </ul>

        <p x-show="! enabled" x-cloak class="px-6 pt-6 pb-8 text-fog">Search isn’t available yet.</p>
        <p x-show="enabled && query.trim().length >= 2 && ! loading && hits.length === 0" x-cloak class="px-6 pb-8 pt-6 text-fog">
            No results for “<span x-text="query.trim()" class="text-chalk"></span>”.
        </p>
        <div class="flex items-center justify-between gap-4 border-t border-line px-5 py-3 font-mono text-xs text-mist">
            <span>↑ ↓ to move · ↵ to open</span>
            <span>Search by Algolia</span>
        </div>
    </dialog>
</div>
