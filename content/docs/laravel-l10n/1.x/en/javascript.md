---
title: 'JavaScript URL Generation'
description: 'Generate localized URLs in your JavaScript or TypeScript frontend with the Wayfinder or Ziggy helper.'
---

The examples on this page use the following route definition:

```php title="routes/web.php"
Route::get('/foo/{id}', Controller::class)
    ->lang(['it', 'es'])
    ->name('foo');
```

## Wayfinder

If you're using Wayfinder, publish the TypeScript helper:

```bash
php artisan vendor:publish --tag=l10n-wayfinder
```

This creates a `resources/js/l10n.ts` file exporting a `route(routes, params?, options?)` helper that selects the appropriate localized route based on the current locale.

> [!NOTE]
> The `route()` helper only works with named routes, actions are not supported.

Import the `route` helper and pass Wayfinder's generated route functions:

```ts
import { route } from '@/l10n';
import foo from '@/routes/foo';

const esUrl = route(foo, { id: 1, lang: 'es' }).url;
```

The `LocalizedRoutes` type is also exported, to type your own wrappers and composables.

The locale is resolved in the following order:

1. The `lang` parameter, if provided.
2. The `lang` attribute of the `<html lang="en">` element, normalized to Laravel's locale format (`pt-BR` matches a `pt_BR` route).
3. The canonical route, when no localized route matches.

During server-side rendering, pass `lang` explicitly to select a localized route; without it, the helper falls back to the canonical route.

To call the canonical route directly, use `foo.__canonical({ id: 1 })`.

## Ziggy

If you're using Ziggy, publish the JavaScript helper:

```bash
php artisan vendor:publish --tag=l10n-ziggy
```

This creates a `resources/js/l10n.js` file with a `route` function.

Use `route()` as a drop-in replacement for Ziggy's `route()` function:

```js
import { route } from '@/l10n';

route('foo', { id: 1, lang: 'it' });
```

The helper uses the `lang` parameter first, then the HTML `lang` attribute, and falls back to the canonical route if no translation matches. Regional locales such as `pt-BR` also match Laravel's underscore format, `pt_BR`.

During server-side rendering, pass `lang` explicitly to select a localized route; without it, the helper falls back to the canonical route.

All Ziggy arguments are forwarded, including an explicit configuration object as the fourth argument. Calling `route()` without a route name returns Ziggy's `Router` instance as usual.
