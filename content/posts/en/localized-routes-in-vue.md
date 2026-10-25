---
title: 'Localized routes in Vue without passing `lang` every time'
published_at: 2026-10-02
package: laravel-l10n
tags: [vue, inertia, ssr]
---

If you use [laravel-l10n](/docs/laravel-l10n) with Vue, its Ziggy and Wayfinder helpers can select localized routes from `<html lang>` in the browser. During server-side rendering there is no document, so without an explicit `lang` they fall back to the canonical route. Passing `lang` on every call fixes that, but gets repetitive. We’ll build a Vue composable that reads the locale from Inertia’s page props instead. Choose the Ziggy or Wayfinder version below for your app; both let you specify `lang` when linking to another language.

<!-- aside: before you start -->
Make sure your Inertia pages receive the current locale as a shared prop. In `app/Http/Middleware/HandleInertiaRequests.php`:

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

<!-- tabs: Pick your stack -->
<!-- tab: Ziggy -->
## Ziggy

With Ziggy installed, create `resources/js/composables/useRoute.js`:

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

Use it in a component:

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

The composable reads `page.props.locale` when generating each URL, so it also picks up locale changes after an Inertia navigation. It checks for the localized route name first (including regional locales such as `pt-BR` and `pt_BR`), then falls back to the canonical name. As with Ziggy itself, SSR needs the Ziggy route configuration available, either globally or as the fourth argument.

To link to another language, pass `lang` in an object:

```js
route('products.show', { product: product.id, lang: 'fr' });
```

The composable uses `lang` to pick the localized route and removes it from a copy of the parameters before calling Ziggy. A missing or undefined `lang` uses the current page’s locale. Calling `route()` without a name still returns Ziggy’s router. The published laravel-l10n Ziggy helper is not needed for this composable.
<!-- tab: Wayfinder -->
## Wayfinder

If your app uses Wayfinder instead of Ziggy, publish the other helper:

```sh title="Terminal"
php artisan vendor:publish --tag=l10n-wayfinder
```

This creates `resources/js/l10n.ts`. Use the same composable name, in `resources/js/composables/useRoute.ts`, for your Wayfinder version:

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

Pass Wayfinder’s generated route object, not a route name. Its `__canonical` export is the fallback when there is no translation for the current locale:

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

The composable selects the function for the page’s locale and forwards the route parameters. It reads the locale from the page props on each call, so it works during SSR and picks up locale changes after an Inertia navigation.

The `typeof route` annotation preserves the published helper’s parameter checks and autocompletion.

To request another language, pass `lang` in an object:

```ts
route(showProduct, { product: product.id, lang: 'fr' }).url;
```

In that case the published helper handles the explicit locale and removes `lang` before calling Wayfinder.
<!-- /tabs -->

If your app stores its current locale somewhere else, replace `page.props.locale` in either version with that source.
