---
title: 'Route localizzate in Vue senza passare `lang` ogni volta'
published_at: 2026-10-02
package: laravel-l10n
slug: route-localizzate-in-vue
tags: [vue, inertia, ssr]
---

Se usi [laravel-l10n](/it/docs/laravel-l10n) con Vue, i suoi helper per Ziggy e Wayfinder scelgono la route localizzata leggendo `<html lang>` nel browser. Durante il rendering lato server però il documento non c'è, quindi senza un `lang` esplicito ripiegano sulla route canonica. Passare `lang` a ogni chiamata risolve il problema, ma diventa ripetitivo. Costruiremo invece un composable Vue che legge la lingua dalle props della pagina Inertia. Scegli qui sotto la versione per Ziggy o per Wayfinder: entrambe ti permettono di indicare `lang` quando crei un link verso un'altra lingua.

<!-- aside: prima di iniziare -->
Assicurati che le pagine Inertia ricevano la lingua corrente come prop condivisa. In `app/Http/Middleware/HandleInertiaRequests.php`:

```php title="HandleInertiaRequests.php"
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'locale' => fn () => app()->getLocale(),
    ];
}
```
<!-- /aside -->

<!-- tabs: Scegli il tuo stack -->
<!-- tab: Ziggy -->
## Ziggy

Con Ziggy installato, crea `resources/js/composables/useRoute.js`:

```js title="resources/js/composables/useRoute.js"
import { usePage } from '@inertiajs/vue3';
import { route as ziggy } from 'ziggy-js';

export function useRoute() {
    const page = usePage();

    return (name, params, absolute = true, config) => {
        if (name === undefined) {
            return ziggy(name, params, absolute, config);
        }

        let locale = page.props.locale;

        if (params !== null && typeof params === 'object' && !Array.isArray(params)
            && 'lang' in params) {
            const { lang, ...rest } = params;
            locale = lang ?? locale;
            params = rest;
        }

        const router = ziggy(undefined, undefined, absolute, config);
        const localizedName = [`${name}.${locale}`, `${name}.${locale.replaceAll('-', '_')}`]
            .find((candidate) => router.has(candidate));

        return ziggy(localizedName ?? name, params, absolute, config);
    };
}
```

Usalo in un componente:

```vue title="Component"
<script setup>
import { Link } from '@inertiajs/vue3';
import { useRoute } from '@/composables/useRoute';

defineProps({ product: Object });

const route = useRoute();
</script>

<template>
    <Link :href="route('products.show', { product: product.id })">
        View product
    </Link>
</template>
```

Il composable legge `page.props.locale` ogni volta che genera un URL, quindi segue anche i cambi di lingua dopo una navigazione Inertia. Cerca prima il nome della route localizzata (comprese le varianti regionali come `pt-BR` e `pt_BR`) e poi ripiega sul nome canonico. Come per Ziggy stesso, in SSR la configurazione delle route di Ziggy deve essere disponibile, globalmente oppure come quarto argomento.

Per creare un link verso un'altra lingua, passa `lang` in un oggetto:

```js
route('products.show', { product: product.id, lang: 'fr' });
```

Il composable usa `lang` per scegliere la route localizzata e lo rimuove da una copia dei parametri prima di chiamare Ziggy. Se `lang` manca o è undefined, viene usata la lingua della pagina corrente. Chiamare `route()` senza nome restituisce ancora il router di Ziggy. Per questo composable non serve l'helper Ziggy pubblicato da laravel-l10n.
<!-- tab: Wayfinder -->
## Wayfinder

Se la tua app usa Wayfinder al posto di Ziggy, pubblica l'altro helper:

```sh title="Terminal"
php artisan vendor:publish --tag=l10n-wayfinder
```

Il comando crea `resources/js/l10n.ts`. Per la versione Wayfinder usa lo stesso nome di composable, in `resources/js/composables/useRoute.ts`:

```ts title="resources/js/composables/useRoute.ts"
import { usePage } from '@inertiajs/vue3';
import { route, type LocalizedRoutes } from '@/l10n';

export function useRoute() {
    const page = usePage<{ locale: string }>();

    const localizedRoute: typeof route = (
        routes: LocalizedRoutes<(...args: any[]) => any>,
        params?: unknown,
        options?: unknown,
    ) => {
        if (params !== null && typeof params === 'object' && !Array.isArray(params)
            && typeof (params as { lang?: unknown }).lang === 'string') {
            return route(routes, params, options);
        }

        const locale = page.props.locale.replaceAll('-', '_');
        const selectedRoute = routes[locale] ?? routes.__canonical;

        return selectedRoute(params, options);
    };

    return localizedRoute;
}
```

Passa l'oggetto route generato da Wayfinder, non il nome della route. Il suo export `__canonical` è il ripiego quando non esiste una traduzione per la lingua corrente:

```vue title="Component"
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useRoute } from '@/composables/useRoute';
import showProduct from '@/routes/products/show';

defineProps<{ product: { id: number } }>();

const route = useRoute();
</script>

<template>
    <Link :href="route(showProduct, { product: product.id }).url">
        View product
    </Link>
</template>
```

Il composable sceglie la funzione per la lingua della pagina e inoltra i parametri della route. Legge la lingua dalle props della pagina a ogni chiamata, quindi funziona in SSR e segue i cambi di lingua dopo una navigazione Inertia.

L'annotazione `typeof route` conserva i controlli sui parametri e l'autocompletamento dell'helper pubblicato.

Per richiedere un'altra lingua, passa `lang` in un oggetto:

```ts
route(showProduct, { product: product.id, lang: 'fr' }).url;
```

In quel caso è l'helper pubblicato a gestire la lingua esplicita, e rimuove `lang` prima di chiamare Wayfinder.
<!-- /tabs -->

Se la tua app conserva la lingua corrente da un'altra parte, sostituisci `page.props.locale` in entrambe le versioni con quella sorgente.
