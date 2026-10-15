@props(['package', 'version', 'page', 'navigation', 'toc' => [], 'title' => null])

@php
    $pageTitle = $title ? "{$title} · {$package['name']}" : $package['name'];
@endphp

<x-layouts.base :title="$pageTitle">
    <x-package-scope :package="$package" class="min-h-screen [&_.c]:text-slate [&_.f]:text-cyan [&_.k]:text-pink [&_.p]:text-peach [&_.s]:text-acid [&_.t]:text-sun">
        <header class="border-b border-ink-4">
            <div class="mx-auto flex max-w-[1360px] flex-wrap items-center gap-5 px-5 py-3.5 md:px-6">
                <a href="{{ route('docs.index', $package['slug']) }}" aria-label="{{ $package['name'] }} documentation home" class="flex items-center gap-3 text-xl font-extrabold tracking-[-0.03em] hover:text-acid">
                    <x-site.blob class="size-9 text-white" shape="fill-[url(#docs-mark)]">
                        <svg class="absolute size-0" aria-hidden="true"><defs><radialGradient id="docs-mark" cx="0" cy="1" r="1.414"><stop offset="0" class="[stop-color:var(--pkg-main)]" /><stop offset="0.55" class="[stop-color:var(--pkg-main)]" /><stop offset="1" class="[stop-color:var(--pkg-light)]" /></radialGradient></defs></svg>
                        <x-site.package-icon :package="$package" stroke-width="1.8" class="size-6" />
                    </x-site.blob>
                    {{ $package['name'] }}
                </a>
                <a href="{{ route('home') }}" class="flex items-center gap-1.5 rounded-full bg-ink-3 py-1.5 pr-3 pl-1.5 text-[13px] text-fog">
                    <x-site.blob class="size-[22px] text-ink">
                        <x-site.cat-mark :stroke-width="5.5" class="size-[17px]" />
                    </x-site.blob>
                    by goodcat
                </a>
                <nav aria-label="Project" class="ml-auto flex flex-wrap items-center gap-1 text-[15px]">
                    <a href="{{ $package['packagist'] }}" class="hidden px-3 py-2.5 text-fog sm:block">Packagist</a>
                    <a href="{{ $package['repository'] }}/releases" class="hidden px-3 py-2.5 text-fog sm:block">Changelog</a>
                    <a href="{{ $package['repository'] }}/issues" class="hidden px-3 py-2.5 text-fog sm:block">Issues</a>
                    <a href="{{ $package['repository'] }}" class="ml-2 inline-flex h-11 items-center gap-2 rounded-full bg-chalk px-[18px] font-bold text-ink hover:text-ink">
                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.58 2 12.23c0 4.52 2.87 8.35 6.84 9.7.5.1.68-.22.68-.49v-1.7c-2.78.62-3.37-1.38-3.37-1.38-.46-1.18-1.11-1.5-1.11-1.5-.91-.64.07-.62.07-.62 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.34 1.12 2.91.86.09-.67.35-1.12.63-1.38-2.22-.26-4.56-1.14-4.56-5.07 0-1.12.39-2.04 1.03-2.76-.1-.26-.45-1.3.1-2.71 0 0 .84-.28 2.75 1.05a9.3 9.3 0 0 1 5 0c1.91-1.33 2.75-1.05 2.75-1.05.55 1.41.2 2.45.1 2.71.64.72 1.03 1.64 1.03 2.76 0 3.94-2.34 4.81-4.57 5.06.36.32.68.94.68 1.9v2.82c0 .27.18.6.69.49A10.1 10.1 0 0 0 22 12.23C22 6.58 17.52 2 12 2Z" /></svg>
                        GitHub
                    </a>
                </nav>
            </div>
        </header>

        <div class="mx-auto flex max-w-[1360px] flex-col px-5 md:px-6 lg:flex-row lg:gap-14">
            <aside x-data="disclosure" class="border-b border-ink-4 py-4 lg:w-60 lg:shrink-0 lg:border-0 lg:py-10">
                <button type="button" x-on:click="toggle" x-bind:aria-expanded="open" aria-controls="docs-nav" class="flex h-11 w-full cursor-pointer items-center justify-between rounded-full bg-ink-3 px-5 font-bold lg:hidden">
                    Menu
                    <svg class="size-5 transition-transform" x-bind:class="open && 'rotate-180'" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 8 5 5 5-5" /></svg>
                </button>
                <nav id="docs-nav" aria-label="Documentation" x-bind:class="open ? 'flex' : 'hidden lg:flex'" class="mt-4 hidden flex-col gap-7 text-[15px] lg:sticky lg:top-10 lg:mt-0 lg:flex">
                    @foreach ($navigation as $section => $pages)
                        <div>
                            <h2 class="mb-2 ml-3 font-mono text-xs font-normal text-mist">// {{ $section }}</h2>
                            @foreach ($pages as $slug => $label)
                                @if ($slug === $page)
                                    <a href="{{ route('docs.show', [$package['slug'], $version, $slug]) }}" aria-current="page" class="block rounded-[14px] bg-pkg px-3 py-2.5 font-bold text-pkg-ink squircle hover:text-pkg-ink">{{ $label }}</a>
                                    @if ($toc)
                                        <div class="my-1.5 ml-[18px] flex flex-col border-l-2 border-line pl-3.5">
                                            @foreach ($toc as $id => $heading)
                                                <a href="#{{ $id }}" class="rounded-[10px] px-2.5 py-[7px] text-sm text-mist hover:bg-ink-3 hover:text-chalk">{{ $heading }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <a href="{{ route('docs.show', [$package['slug'], $version, $slug]) }}" class="block rounded-[14px] px-3 py-2.5 text-fog squircle hover:bg-ink-3">{{ $label }}</a>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </nav>
            </aside>

            <main class="min-w-0 max-w-[900px] flex-1 pt-10 pb-16 [&_:not(pre)>code]:rounded-lg [&_:not(pre)>code]:bg-ink-4 [&_:not(pre)>code]:px-[7px] [&_:not(pre)>code]:py-0.5 [&_:not(pre)>code]:font-mono [&_:not(pre)>code]:text-[.86em] [&_:not(pre)>code]:text-chalk [&>h2]:mt-18 [&>h2]:scroll-mt-6 [&>h2]:text-[40px] [&>h2]:font-extrabold [&>h2]:tracking-[-0.035em] [&>p]:mt-4 [&>p]:text-lg [&>p]:leading-[1.7] [&>p]:text-fog [&_strong]:font-bold [&_strong]:text-white">
                {{ $slot }}

                <footer class="mt-12 flex flex-wrap justify-between gap-4 border-t border-ink-4 pt-6 text-sm text-mist">
                    <span>© goodcat.dev · Design, Code, Repeat.</span>
                    <nav aria-label="Footer" class="flex gap-5">
                        <a href="{{ route('home') }}" class="hover:text-acid">goodcat.dev</a>
                        <a href="{{ $package['repository'] }}" class="hover:text-acid">GitHub</a>
                        <a href="{{ $package['packagist'] }}" class="hover:text-acid">Packagist</a>
                    </nav>
                </footer>
            </main>
        </div>
    </x-package-scope>
</x-layouts.base>
