<x-layouts.post :post="$post" :package="$package" :previous="$previous" :next="$next">
    <x-slot:title>Localized routes in Vue without passing <span class="inline-block -rotate-2 rounded-[32px] bg-acid px-[22px] pb-1.5 font-mono text-[0.8em] font-medium tracking-[-0.04em] text-ink squircle">lang</span> every time</x-slot:title>

    <p class="mt-0! text-[23px]! leading-[1.6]! text-zinc-200!">If you use <a href="{{ $package['url'] }}">laravel-l10n</a> with Vue, its Ziggy and Wayfinder helpers can select localized routes from <code>&lt;html lang&gt;</code> in the browser. During server-side rendering there is no document, so without an explicit <code>lang</code> they fall back to the canonical route. Passing <code>lang</code> on every call fixes that, but gets repetitive. We’ll build a Vue composable that reads the locale from Inertia’s page props instead. Choose the Ziggy or Wayfinder version below for your app; both let you specify <code>lang</code> when linking to another language.</p>

    <div class="mt-12 rounded-[40px] bg-ink-3 p-8 squircle">
        <span class="font-mono text-[13px] text-acid">// before you start</span>
        <p class="mt-3!">Make sure your Inertia pages receive the current locale as a shared prop. In <code>app/Http/Middleware/HandleInertiaRequests.php</code>:</p>
        <x-code-block file="HandleInertiaRequests.php" lang="php" dot="bg-[#7c5cff]" class="bg-ink"><span class="k">public function</span> <span class="f">share</span>(<span class="t">Request</span> <span class="p">$request</span>): <span class="k">array</span>
{
    <span class="k">return</span> [
        ...<span class="k">parent</span>::<span class="f">share</span>(<span class="p">$request</span>),
        <span class="s">'locale'</span> =&gt; <span class="k">fn</span> () =&gt; <span class="f">app</span>()-&gt;<span class="f">getLocale</span>(),
    ];
}</x-code-block>
    </div>

    <div x-data="tabs('ziggy')">
        <div class="sticky top-4 z-10 mt-14 flex flex-wrap items-center justify-between gap-4 rounded-full bg-ink-3/92 py-2 pr-2 pl-6 shadow-[0_12px_30px_-12px_rgba(0,0,0,0.6)] backdrop-blur">
            <span id="stack-label" class="text-base font-bold">Pick your stack</span>
            <div role="tablist" aria-labelledby="stack-label" class="flex gap-1 rounded-full bg-ink p-1">
                @foreach (['ziggy' => 'Ziggy', 'wayfinder' => 'Wayfinder'] as $stack => $label)
                    <button
                        type="button"
                        role="tab"
                        id="tab-{{ $stack }}"
                        aria-controls="panel-{{ $stack }}"
                        x-on:click="select('{{ $stack }}')"
                        x-bind:aria-selected="isActive('{{ $stack }}')"
                        x-bind:class="isActive('{{ $stack }}') ? 'bg-acid text-ink' : 'text-fog hover:text-chalk'"
                        class="h-11 cursor-pointer rounded-full px-[22px] text-base font-bold"
                    >{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <section id="panel-ziggy" role="tabpanel" aria-labelledby="tab-ziggy" x-show="isActive('ziggy')">
            <h2 id="ziggy">Ziggy</h2>
            <p>With Ziggy installed, create <code>resources/js/composables/useRoute.js</code>:</p>
            <x-code-block file="resources/js/composables/useRoute.js" lang="js" dot="bg-sun"><span class="k">import</span> { usePage } <span class="k">from</span> <span class="s">'@inertiajs/vue3'</span>;
<span class="k">import</span> { route <span class="k">as</span> ziggy } <span class="k">from</span> <span class="s">'ziggy-js'</span>;

<span class="k">export function</span> <span class="f">useRoute</span>() {
    <span class="k">const</span> page = <span class="f">usePage</span>();

    <span class="k">return</span> (<span class="p">name</span>, <span class="p">params</span>, <span class="p">absolute</span> = <span class="k">true</span>, <span class="p">config</span>) =&gt; {
        <span class="k">if</span> (name === <span class="k">undefined</span>) {
            <span class="k">return</span> <span class="f">ziggy</span>(name, params, absolute, config);
        }

        <span class="k">let</span> locale = page.props.locale;

        <span class="k">if</span> (params !== <span class="k">null</span> &amp;&amp; <span class="k">typeof</span> params === <span class="s">'object'</span> &amp;&amp; !Array.<span class="f">isArray</span>(params)
            &amp;&amp; <span class="s">'lang'</span> <span class="k">in</span> params) {
            <span class="k">const</span> { lang, ...rest } = params;
            locale = lang ?? locale;
            params = rest;
        }

        <span class="k">const</span> router = <span class="f">ziggy</span>(<span class="k">undefined</span>, <span class="k">undefined</span>, absolute, config);
        <span class="k">const</span> localizedName = [<span class="s">`${name}.${locale}`</span>, <span class="s">`${name}.${locale.replaceAll('-', '_')}`</span>]
            .<span class="f">find</span>((<span class="p">candidate</span>) =&gt; router.<span class="f">has</span>(candidate));

        <span class="k">return</span> <span class="f">ziggy</span>(localizedName ?? name, params, absolute, config);
    };
}</x-code-block>
            <p>Use it in a component:</p>
            <x-code-block file="Component" lang="vue" dot="bg-mint"><span class="t">&lt;script</span> <span class="p">setup</span><span class="t">&gt;</span>
<span class="k">import</span> { Link } <span class="k">from</span> <span class="s">'@inertiajs/vue3'</span>;
<span class="k">import</span> { useRoute } <span class="k">from</span> <span class="s">'@/composables/useRoute'</span>;

<span class="f">defineProps</span>({ product: Object });

<span class="k">const</span> route = <span class="f">useRoute</span>();
<span class="t">&lt;/script&gt;</span>

<span class="t">&lt;template&gt;</span>
    <span class="t">&lt;Link</span> <span class="p">:href</span>=<span class="s">"route('products.show', { product: product.id })"</span><span class="t">&gt;</span>
        View product
    <span class="t">&lt;/Link&gt;</span>
<span class="t">&lt;/template&gt;</span></x-code-block>
            <p>The composable reads <code>page.props.locale</code> when generating each URL, so it also picks up locale changes after an Inertia navigation. It checks for the localized route name first (including regional locales such as <code>pt-BR</code> and <code>pt_BR</code>), then falls back to the canonical name. As with Ziggy itself, SSR needs the Ziggy route configuration available, either globally or as the fourth argument.</p>
            <p>To link to another language, pass <code>lang</code> in an object:</p>
            <x-code-block><span class="f">route</span>(<span class="s">'products.show'</span>, { product: product.id, lang: <span class="s">'fr'</span> });</x-code-block>
            <p>The composable uses <code>lang</code> to pick the localized route and removes it from a copy of the parameters before calling Ziggy. A missing or undefined <code>lang</code> uses the current page’s locale. Calling <code>route()</code> without a name still returns Ziggy’s router. The published laravel-l10n Ziggy helper is not needed for this composable.</p>
        </section>

        <section id="panel-wayfinder" role="tabpanel" aria-labelledby="tab-wayfinder" x-show="isActive('wayfinder')" x-cloak>
            <h2 id="wayfinder">Wayfinder</h2>
            <p>If your app uses Wayfinder instead of Ziggy, publish the other helper:</p>
            <x-code-block file="Terminal" lang="sh"><span class="f">php</span> artisan vendor:publish <span class="p">--tag=l10n-wayfinder</span></x-code-block>
            <p>This creates <code>resources/js/l10n.ts</code>. Use the same composable name, in <code>resources/js/composables/useRoute.ts</code>, for your Wayfinder version:</p>
            <x-code-block file="resources/js/composables/useRoute.ts" lang="ts" dot="bg-cobalt"><span class="k">import</span> { usePage } <span class="k">from</span> <span class="s">'@inertiajs/vue3'</span>;
<span class="k">import</span> { route, <span class="k">type</span> LocalizedRoutes } <span class="k">from</span> <span class="s">'@/l10n'</span>;

<span class="k">export function</span> <span class="f">useRoute</span>() {
    <span class="k">const</span> page = <span class="f">usePage</span>&lt;{ locale: <span class="t">string</span> }&gt;();

    <span class="k">const</span> localizedRoute: <span class="k">typeof</span> route = (
        <span class="p">routes</span>: <span class="t">LocalizedRoutes</span>&lt;(...args: <span class="t">any</span>[]) =&gt; <span class="t">any</span>&gt;,
        <span class="p">params</span>?: <span class="t">unknown</span>,
        <span class="p">options</span>?: <span class="t">unknown</span>,
    ) =&gt; {
        <span class="k">if</span> (params !== <span class="k">null</span> &amp;&amp; <span class="k">typeof</span> params === <span class="s">'object'</span> &amp;&amp; !Array.<span class="f">isArray</span>(params)
            &amp;&amp; <span class="k">typeof</span> (params <span class="k">as</span> { lang?: <span class="t">unknown</span> }).lang === <span class="s">'string'</span>) {
            <span class="k">return</span> <span class="f">route</span>(routes, params, options);
        }

        <span class="k">const</span> locale = page.props.locale.<span class="f">replaceAll</span>(<span class="s">'-'</span>, <span class="s">'_'</span>);
        <span class="k">const</span> selectedRoute = routes[locale] ?? routes.__canonical;

        <span class="k">return</span> <span class="f">selectedRoute</span>(params, options);
    };

    <span class="k">return</span> localizedRoute;
}</x-code-block>
            <p>Pass Wayfinder’s generated route object, not a route name. Its <code>__canonical</code> export is the fallback when there is no translation for the current locale:</p>
            <x-code-block file="Component" lang="vue" dot="bg-mint"><span class="t">&lt;script</span> <span class="p">setup lang</span>=<span class="s">"ts"</span><span class="t">&gt;</span>
<span class="k">import</span> { Link } <span class="k">from</span> <span class="s">'@inertiajs/vue3'</span>;
<span class="k">import</span> { useRoute } <span class="k">from</span> <span class="s">'@/composables/useRoute'</span>;
<span class="k">import</span> showProduct <span class="k">from</span> <span class="s">'@/routes/products/show'</span>;

<span class="f">defineProps</span>&lt;{ product: { id: <span class="t">number</span> } }&gt;();

<span class="k">const</span> route = <span class="f">useRoute</span>();
<span class="t">&lt;/script&gt;</span>

<span class="t">&lt;template&gt;</span>
    <span class="t">&lt;Link</span> <span class="p">:href</span>=<span class="s">"route(showProduct, { product: product.id }).url"</span><span class="t">&gt;</span>
        View product
    <span class="t">&lt;/Link&gt;</span>
<span class="t">&lt;/template&gt;</span></x-code-block>
            <p>The composable selects the function for the page’s locale and forwards the route parameters. It reads the locale from the page props on each call, so it works during SSR and picks up locale changes after an Inertia navigation.</p>
            <p>The <code>typeof route</code> annotation preserves the published helper’s parameter checks and autocompletion.</p>
            <p>To request another language, pass <code>lang</code> in an object:</p>
            <x-code-block><span class="f">route</span>(showProduct, { product: product.id, lang: <span class="s">'fr'</span> }).url;</x-code-block>
            <p>In that case the published helper handles the explicit locale and removes <code>lang</code> before calling Wayfinder.</p>
        </section>
    </div>

    <p class="mt-10!">If your app stores its current locale somewhere else, replace <code>page.props.locale</code> in either version with that source.</p>
</x-layouts.post>
