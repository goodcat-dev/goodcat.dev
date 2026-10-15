@php
    $bullet = 'mt-[9px] size-2.5 shrink-0 rounded bg-pkg squircle';
    $guides = [
        'localized-routes' => ['Localized Routes', 'Define localized routes and translate their URIs and domains.'],
        'url-generation' => ['URL Generation', 'Generate URLs in the current language or in any other one.'],
        'locale-preference' => ['Locale Preference', 'Detect the user’s preferred language and redirect to it.'],
        'javascript' => ['JavaScript', 'Generate localized URLs with Wayfinder or Ziggy.'],
    ];
@endphp

<x-layouts.docs :package="$package" :version="$version" :page="$page" :navigation="$navigation" title="Introduction" :toc="['at-a-glance' => 'At a glance', 'built-on-laravel' => 'Built on Laravel', 'guides' => 'Guides', 'features' => 'Features']">
    <section class="rounded-[48px] bg-[radial-gradient(circle_at_0_100%,var(--pkg-main)_50%,var(--pkg-light)_130%)] px-7 pt-12 pb-10 text-pkg-ink squircle md:px-12 md:pt-14 md:pb-12">
        <p class="font-mono text-[13px]">composer require goodcat/{{ $package['slug'] }}</p>
        <h1 class="mt-[18px] text-[56px] leading-[0.95] font-extrabold tracking-[-0.05em] md:text-[88px]">{{ $package['title'] }}</h1>
        <p class="mt-[22px] max-w-[640px] text-[21px] leading-normal">An opinionated Laravel package for app localization. Define localized routes, translate their URIs and domains, generate URLs in any language, and detect the user’s preferred locale.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('docs.show', [$package['slug'], $version, 'quickstart']) }}" class="inline-flex h-[50px] items-center gap-2.5 rounded-full bg-ink px-6 text-[17px] font-bold text-chalk">
                Quickstart
                <svg class="size-[18px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 10h10M11 6l4 4-4 4" /></svg>
            </a>
            <a href="{{ $package['repository'] }}" class="inline-flex h-[50px] items-center rounded-full px-6 text-[17px] font-bold text-pkg-ink shadow-[inset_0_0_0_2px_var(--pkg-ink)] hover:text-pkg-ink">View on GitHub</a>
        </div>
    </section>

    <h2 id="at-a-glance">At a glance</h2>
    <p>Add <code>lang()</code> to a route and list the languages it should be available in. Translate its URI in your language files, if you want to:</p>

    <x-code-tabs :files="['routes/web.php', 'lang/es/routes.php']">
        <x-code-tabs.panel :index="0"><span class="t">Route</span>::<span class="f">get</span>(<span class="s">'/products/{product}'</span>, [<span class="t">ProductController</span>::<span class="k">class</span>, <span class="s">'show'</span>])
    -&gt;<span class="f">lang</span>([<span class="s">'es'</span>, <span class="s">'it'</span>])
    -&gt;<span class="f">name</span>(<span class="s">'products.show'</span>);</x-code-tabs.panel>
        <x-code-tabs.panel :index="1"><span class="k">return</span> [
    <span class="s">'products/{product}'</span> =&gt; <span class="s">'productos/{product}'</span>,
];</x-code-tabs.panel>
    </x-code-tabs>

    <p class="mt-7!">Your application now serves the same page in three languages, each one with its own URL and its own application locale:</p>
    <ul class="mt-4 flex flex-col gap-2.5 text-lg text-fog">
        <li class="flex flex-wrap items-center gap-3.5"><span aria-hidden="true" class="size-2.5 rounded bg-pkg squircle"></span><code>/products/42</code> in English</li>
        <li class="flex flex-wrap items-center gap-3.5"><span aria-hidden="true" class="size-2.5 rounded bg-pkg squircle"></span><code>/es/productos/42</code> in Spanish</li>
        <li class="flex flex-wrap items-center gap-3.5"><span aria-hidden="true" class="size-2.5 rounded bg-pkg squircle"></span><code>/it/products/42</code> in Italian</li>
    </ul>

    <p class="mt-7!">Links keep working as before, and follow the language of the page the user is reading:</p>
    <x-code-block class="mt-5"><span class="f">route</span>(<span class="s">'products.show'</span>, $product); <span class="c">// current language</span>
<span class="f">route</span>(<span class="s">'products.show'</span>, [$product, <span class="s">'lang'</span> =&gt; <span class="s">'es'</span>]); <span class="c">// "/es/productos/42"</span></x-code-block>

    <h2 id="built-on-laravel">Built on Laravel</h2>
    <p><code>laravel-l10n</code> adds a single method to your routes and relies on what Laravel already provides for everything else:</p>
    <ul class="mt-4 flex flex-col gap-3 text-lg leading-[1.6] text-fog">
        <li class="flex gap-3.5"><span aria-hidden="true" class="{{ $bullet }}"></span><span>the <strong>fallback locale</strong> from <code>config/app.php</code> is the language of your original routes;</span></li>
        <li class="flex gap-3.5"><span aria-hidden="true" class="{{ $bullet }}"></span><span><strong>language files</strong> in <code>lang/</code> hold the translated URIs, next to your other translations;</span></li>
        <li class="flex gap-3.5"><span aria-hidden="true" class="{{ $bullet }}"></span><span>the <strong><code>route()</code> and <code>action()</code> helpers</strong>, redirects and signed URLs generate localized URLs without new APIs;</span></li>
        <li class="flex gap-3.5"><span aria-hidden="true" class="{{ $bullet }}"></span><span><strong>route caching</strong>, middleware, route model binding and route groups work as usual on translated routes;</span></li>
        <li class="flex gap-3.5"><span aria-hidden="true" class="{{ $bullet }}"></span><span>the <strong><code>HasLocalePreference</code></strong> interface, already used by notifications and mailables, provides the user’s preferred language.</span></li>
    </ul>
    <p class="mt-5!">If you know Laravel’s routing, you already know most of <code>laravel-l10n</code>.</p>
    <a href="{{ route('docs.show', [$package['slug'], $version, 'quickstart']) }}" class="mt-5 inline-flex min-h-11 items-center gap-2 text-[17px] font-bold text-pkg-text hover:text-acid">
        Get started with the Quickstart
        <svg class="size-[18px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 10h10M11 6l4 4-4 4" /></svg>
    </a>

    <h2 id="guides">Guides</h2>
    <div class="mt-6 grid gap-3.5 sm:grid-cols-2">
        @foreach ($guides as $slug => [$heading, $description])
            <a href="{{ route('docs.show', [$package['slug'], $version, $slug]) }}" class="flex flex-col gap-2.5 rounded-[32px] bg-ink-2 p-7 transition-transform duration-250 squircle hover:-translate-y-[3px] hover:text-chalk">
                <span class="font-mono text-xs text-pkg-text">{{ sprintf('%02d', $loop->iteration) }}</span>
                <span class="text-[22px] font-extrabold tracking-[-0.02em]">{{ $heading }}</span>
                <span class="text-base leading-[1.55] text-mist">{{ $description }}</span>
                <span class="mt-auto pt-2 font-bold text-pkg-text">Read more →</span>
            </a>
        @endforeach
    </div>

    <h2 id="features">Features</h2>
    <div class="mt-6 flex flex-wrap gap-3.5">
        <a href="{{ route('docs.show', [$package['slug'], $version, 'helpers']) }}" class="flex min-h-60 flex-[1_1_240px] flex-col gap-3 rounded-[32px] bg-pkg p-7 text-pkg-ink transition-transform duration-250 squircle hover:-translate-y-[3px] hover:text-pkg-ink">
            <svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205 12 12m6.894 5.785-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" /></svg>
            <span class="mt-auto text-[22px] font-extrabold tracking-[-0.02em]">Helpers</span>
            <span class="text-base leading-normal">Read the user’s preferred locales, get the route’s language, and match routes regardless of the locale.</span>
        </a>
        <a href="{{ route('docs.show', [$package['slug'], $version, 'blade-components']) }}" class="flex min-h-60 flex-[1_1_240px] flex-col gap-3 rounded-[32px] bg-ink-2 p-7 shadow-[inset_0_0_0_2px_var(--pkg-main)] transition-transform duration-250 squircle hover:-translate-y-[3px] hover:text-chalk">
            <svg class="size-10 text-pkg-text" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
            <span class="mt-auto text-[22px] font-extrabold tracking-[-0.02em]">Blade Components</span>
            <span class="text-base leading-normal text-fog">Render alternate hreflang links and a locale switcher in your Blade layouts.</span>
        </a>
        <a href="{{ route('docs.show', [$package['slug'], $version, 'localized-views']) }}" class="flex min-h-60 flex-[1_1_240px] flex-col gap-3 rounded-[32px] bg-pkg-light p-7 text-ink transition-transform duration-250 squircle hover:-translate-y-[3px] hover:text-ink">
            <svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
            <span class="mt-auto text-[22px] font-extrabold tracking-[-0.02em]">Localized Views</span>
            <span class="text-base leading-normal">Organize language-specific Blade views in folders named after their locale.</span>
        </a>
    </div>

    <div x-data="feedback" class="mt-18 flex min-h-11 flex-wrap items-center gap-4">
        <template x-if="! voted">
            <div class="flex flex-wrap items-center gap-4">
                <span class="text-base text-fog">Was this page helpful?</span>
                <span class="flex gap-2">
                    <button type="button" x-on:click="vote" class="h-11 cursor-pointer rounded-full bg-ink-3 px-5 font-bold hover:text-acid">Yes</button>
                    <button type="button" x-on:click="vote" class="h-11 cursor-pointer rounded-full bg-ink-3 px-5 font-bold hover:text-acid">No</button>
                </span>
            </div>
        </template>
        <template x-if="voted">
            <span class="inline-flex items-center gap-2.5 text-base text-fog" role="status">
                <x-site.cat-mark :stroke-width="5" class="size-6 text-pkg-text" />
                Thanks for your feedback!
            </span>
        </template>
    </div>

    <a href="{{ route('docs.show', [$package['slug'], $version, 'quickstart']) }}" class="mt-7 flex items-center justify-between gap-4 rounded-[32px] bg-ink-2 px-8 py-[26px] transition-transform duration-250 squircle hover:-translate-y-[3px] hover:text-chalk">
        <span>
            <span class="block font-mono text-xs text-mist">next</span>
            <span class="mt-1 block text-[26px] font-extrabold tracking-[-0.03em]">Quickstart</span>
        </span>
        <x-site.blob class="size-13 text-pkg-ink" shape="fill-pkg">
            <svg class="size-[22px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 10h10M11 6l4 4-4 4" /></svg>
        </x-site.blob>
    </a>
</x-layouts.docs>
