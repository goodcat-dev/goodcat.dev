@props(['package', 'version', 'current', 'navigation', 'toc' => [], 'title' => null])

@php
    $pageTitle = $title ? "{$title} · {$package['name']}" : $package['name'];
@endphp

<x-layouts.base :title="$pageTitle">
    <x-package-scope :package="$package" x-data="disclosure" x-on:keydown.escape.window="open = false" class="min-h-screen">
        <svg class="absolute size-0" aria-hidden="true"><defs><radialGradient id="docs-mark" cx="0" cy="1" r="1.414"><stop offset="0" class="[stop-color:var(--pkg-main)]" /><stop offset="0.55" class="[stop-color:var(--pkg-main)]" /><stop offset="1" class="[stop-color:var(--pkg-light)]" /></radialGradient></defs></svg>

        <header class="sticky top-0 z-30 border-b border-ink-4 bg-ink/85 backdrop-blur-md">
            <div class="mx-auto flex h-[72px] max-w-[1360px] items-center gap-3 px-5 md:gap-5 md:px-6">
                <x-docs.logo :package="$package" />
                <x-docs.search :package="$package" :version="$version" class="ml-auto md:ml-6 md:max-w-[380px] md:flex-[0_1_380px]" />
                <nav aria-label="Project" class="flex items-center gap-1 text-[15px] md:ml-auto">
                    <a href="{{ $package['packagist'] }}" class="hidden px-3 py-2.5 text-fog xl:block">Packagist</a>
                    <a href="{{ $package['repository'] }}/blob/main/CHANGELOG.md" class="hidden px-3 py-2.5 text-fog xl:block">Changelog</a>
                    <a href="{{ $package['repository'] }}/issues" class="hidden px-3 py-2.5 text-fog xl:block">Issues</a>
                    <a href="{{ $package['repository'] }}" aria-label="GitHub" class="inline-flex h-11 items-center gap-2 rounded-full bg-chalk px-[13px] font-bold text-ink hover:text-ink sm:px-[18px] xl:ml-2">
                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.58 2 12.23c0 4.52 2.87 8.35 6.84 9.7.5.1.68-.22.68-.49v-1.7c-2.78.62-3.37-1.38-3.37-1.38-.46-1.18-1.11-1.5-1.11-1.5-.91-.64.07-.62.07-.62 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.34 1.12 2.91.86.09-.67.35-1.12.63-1.38-2.22-.26-4.56-1.14-4.56-5.07 0-1.12.39-2.04 1.03-2.76-.1-.26-.45-1.3.1-2.71 0 0 .84-.28 2.75 1.05a9.3 9.3 0 0 1 5 0c1.91-1.33 2.75-1.05 2.75-1.05.55 1.41.2 2.45.1 2.71.64.72 1.03 1.64 1.03 2.76 0 3.94-2.34 4.81-4.57 5.06.36.32.68.94.68 1.9v2.82c0 .27.18.6.69.49A10.1 10.1 0 0 0 22 12.23C22 6.58 17.52 2 12 2Z" /></svg>
                        <span class="hidden sm:inline">GitHub</span>
                    </a>
                    <button type="button" x-on:click="toggle" x-bind:aria-expanded="open" x-bind:aria-label="open ? 'Close navigation' : 'Open navigation'" aria-label="Open navigation" aria-controls="docs-mobile-nav" class="ml-1 flex size-11 cursor-pointer items-center justify-center rounded-full bg-ink-3 text-chalk lg:hidden">
                        <svg x-show="! open" class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 6h14M3 10h14M3 14h14" /></svg>
                        <svg x-show="open" x-cloak class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m5 5 10 10M15 5 5 15" /></svg>
                    </button>
                </nav>
            </div>
            <div id="docs-mobile-nav" x-show="open" x-cloak x-on:click="$event.target.closest('a') && (open = false)" class="max-h-[calc(100dvh-72px)] overflow-y-auto overscroll-contain border-t border-ink-4 px-5 pt-6 pb-10 md:px-6 lg:hidden">
                <x-docs.navigation :package="$package" :version="$version" :current="$current" :navigation="$navigation" :toc="$toc" class="-ml-3" />
            </div>
        </header>

        <div class="mx-auto flex max-w-[1360px] gap-14 px-5 md:px-6">
            <aside class="sticky top-[72px] hidden self-start max-h-[calc(100dvh-72px)] w-60 shrink-0 overflow-y-auto overscroll-contain py-10 lg:block">
                <x-docs.navigation :package="$package" :version="$version" :current="$current" :navigation="$navigation" :toc="$toc" />
            </aside>

            <main class="min-w-0 max-w-[900px] flex-1 pt-10 pb-16">
                {{ $slot }}

                <footer class="mt-12 flex flex-wrap items-center justify-between gap-4 border-t border-ink-4 pt-6 text-sm text-mist">
                    <span>© goodcat.dev · Design, Code, Repeat.</span>
                    <nav aria-label="Footer" class="flex items-center gap-5">
                        <a href="{{ route('home') }}" aria-label="goodcat.dev" title="goodcat.dev" class="transition-transform hover:-rotate-6">
                            <x-site.blob class="size-8 text-ink">
                                <x-site.cat-mark :stroke-width="5.5" class="size-6" />
                            </x-site.blob>
                        </a>
                        <a href="{{ $package['repository'] }}" class="hover:text-acid">GitHub</a>
                        <a href="{{ $package['packagist'] }}" class="hover:text-acid">Packagist</a>
                    </nav>
                </footer>
            </main>
        </div>
    </x-package-scope>
</x-layouts.base>
