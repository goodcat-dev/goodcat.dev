@props(['package', 'version', 'current', 'navigation', 'toc' => [], 'title' => null])

@php
    $pageTitle = $title ? "{$title} · {$package['name']}" : $package['name'];
@endphp

<x-layouts.base :title="$pageTitle">
    <x-package-scope :package="$package" x-data="disclosure" x-on:keydown.escape.window="open = false" class="min-h-screen">
        <svg class="absolute size-0" aria-hidden="true"><defs><radialGradient id="docs-mark" cx="0" cy="1" r="1.414"><stop offset="0" class="[stop-color:var(--pkg-main)]" /><stop offset="0.55" class="[stop-color:var(--pkg-main)]" /><stop offset="1" class="[stop-color:var(--pkg-light)]" /></radialGradient></defs></svg>

        <header class="sticky top-0 z-30 border-b border-ink-4 bg-ink/85 backdrop-blur-md [view-transition-name:docs-header]">
            <div class="mx-auto flex h-[72px] max-w-[1360px] items-center gap-3 px-5 md:gap-5 md:px-6">
                <x-docs.logo :package="$package" />
                <x-docs.search :package="$package" :version="$version" class="ml-auto md:ml-6 md:max-w-[380px] md:flex-[0_1_380px]" />
                <nav aria-label="{{ __('Project') }}" class="flex items-center gap-1 text-[15px] md:ml-auto">
                    <a href="{{ $package['packagist'] }}" class="hidden px-3 py-2.5 text-fog xl:block">Packagist</a>
                    <a href="{{ $package['repository'] }}/blob/main/CHANGELOG.md" class="hidden px-3 py-2.5 text-fog xl:block">{{ __('Changelog') }}</a>
                    <a href="{{ $package['repository'] }}/issues" class="hidden px-3 py-2.5 text-fog xl:block">{{ __('Issues') }}</a>
                    <x-l10n::switcher class="mr-1 hidden lg:block" />
                    <x-theme-toggle />
                    <a href="{{ $package['repository'] }}" class="hidden h-11 items-center gap-2 rounded-full bg-chalk px-[18px] font-bold text-ink hover:text-ink lg:inline-flex xl:ml-2">
                        <x-icons.github />
                        GitHub
                    </a>
                    <button type="button" x-on:click="toggle" x-bind:aria-expanded="open" x-bind:aria-label="open ? @js(__('Close navigation')) : @js(__('Open navigation'))" aria-label="{{ __('Open navigation') }}" aria-controls="docs-mobile-nav" class="ml-1 flex size-11 cursor-pointer items-center justify-center rounded-full bg-ink-3 text-chalk lg:hidden">
                        <svg x-show="! open" class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 6h14M3 10h14M3 14h14" /></svg>
                        <svg x-show="open" x-cloak class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m5 5 10 10M15 5 5 15" /></svg>
                    </button>
                </nav>
            </div>
            <div id="docs-mobile-nav" x-show="open" x-cloak x-on:click="$event.target.closest('a') && (open = false)" class="max-h-[calc(100dvh-72px)] overflow-y-auto overscroll-contain border-t border-ink-4 px-2 pt-6 pb-10 md:px-3 lg:hidden">
                <x-docs.navigation :package="$package" :version="$version" :current="$current" :navigation="$navigation" :toc="$toc" />
                <div class="mt-10 flex flex-col gap-7 border-t border-ink-4 pt-8 text-[15px]">
                    <div>
                        <h2 class="mb-2 ml-3 font-mono text-xs font-normal text-mist">// {{ __('project') }}</h2>
                        <a href="{{ $package['repository'] }}" class="flex items-center gap-2.5 rounded-[14px] px-3 py-2.5 font-bold text-chalk squircle hover:bg-ink-3">
                            <x-icons.github />
                            GitHub
                        </a>
                        <a href="{{ $package['packagist'] }}" class="block rounded-[14px] px-3 py-2.5 text-fog squircle hover:bg-ink-3">Packagist</a>
                        <a href="{{ $package['repository'] }}/blob/main/CHANGELOG.md" class="block rounded-[14px] px-3 py-2.5 text-fog squircle hover:bg-ink-3">{{ __('Changelog') }}</a>
                        <a href="{{ $package['repository'] }}/issues" class="block rounded-[14px] px-3 py-2.5 text-fog squircle hover:bg-ink-3">{{ __('Issues') }}</a>
                    </div>
                    <x-l10n::switcher variant="list" />
                </div>
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
                    <nav aria-label="{{ __('Footer') }}" class="flex items-center gap-5">
                        <a href="{{ route('home') }}" aria-label="goodcat.dev" title="goodcat.dev" class="transition-transform hover:-rotate-6">
                            <x-site.blob class="size-8 text-on-brand">
                                <x-site.cat-mark :stroke-width="4.4" class="size-6" />
                            </x-site.blob>
                        </a>
                        <a href="{{ $package['repository'] }}" class="hover:text-accent">GitHub</a>
                        <a href="{{ $package['packagist'] }}" class="hover:text-accent">Packagist</a>
                    </nav>
                </footer>
            </main>
        </div>
    </x-package-scope>
</x-layouts.base>
