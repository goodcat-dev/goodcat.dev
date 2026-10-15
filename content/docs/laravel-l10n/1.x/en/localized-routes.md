---
title: 'Localized Routes'
description: 'Define which locales a route supports, choose how locale prefixes are applied, and translate route URIs and domains.'
---

## How it works

Every route you mark with `lang()` becomes the **canonical route**: it keeps its URI and its name, and serves your fallback locale. For each additional locale, the package registers a **translated route**, a copy of the canonical route with:

- a URI prefixed with the locale and, when available, translated through your language files;
- a name made of the canonical name plus the locale, such as `example.es`;
- the application locale set to its language for every request it serves.

Translated routes are generated once all service providers have booted, so you define your routes as usual in your route files and they are picked up automatically. They also work with [route caching](url-generation#route-caching).

## Defining localized routes

Use the `lang()` method to define which locales a route should support:

```php title="routes/web.php"
Route::get('/example', Controller::class)
    ->lang(['es', 'fr', 'it'])
    ->name('example');
```

Assuming the fallback locale is `en` and a Spanish translation for `example` exists in `lang/es/routes.php`, this registers four routes:

| URI | Name | Locale |
| --- | --- | --- |
| `/example` | `example` | `en` |
| `/es/ejemplo` | `example.es` | `es` |
| `/fr/example` | `example.fr` | `fr` |
| `/it/example` | `example.it` | `it` |

The French and Italian routes have no translation defined, so they keep the original URI behind the locale prefix. See [Translating route URIs](#translating-route-uris) to add one.

### The fallback locale

The canonical route serves Laravel's own `fallback_locale` (`APP_FALLBACK_LOCALE` in `.env`). It must match the language your routes and content are actually written in: if your URIs are in English, the fallback locale should be `en`.

Listing the fallback locale in `lang()` is harmless: the canonical route already serves it, so no extra route is registered.

```php title="routes/web.php"
// Same result as ->lang(['es']) when the fallback locale is 'en'
Route::get('/example', Controller::class)
    ->lang(['en', 'es']);
```

## Route groups

To avoid repeating the same languages on every route, use `Route::lang()->group()`:

```php title="routes/web.php"
Route::lang(['es', 'it'])->group(function () {
    Route::get('/example', fn () => 'Hello, World!');
    Route::get('/another', fn () => 'Another route');
});
```

All routes inside the group inherit its locales. Call `lang()` on an individual route to add more languages on top of the group's ones:

```php title="routes/web.php"
Route::lang(['es', 'it'])->group(function () {
    Route::get('/example', fn () => 'Hello, World!'); // es, it
    Route::get('/another', fn () => 'Another route')
        ->lang(['fr']); // es, it, fr
});
```

`lang()` combines with the other group attributes, such as middleware, prefixes and domains:

```php title="routes/web.php"
Route::middleware('auth')->lang(['es', 'it'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});
```

## The application locale

Every localized route sets the application locale to its own language before your code runs:

- a translated route uses its locale, so `/es/ejemplo` runs with `app()->getLocale()` equal to `es`;
- the canonical route uses the fallback locale, so `/example` runs with `en`;
- routes without `lang()` leave the application locale unchanged, which defaults to `APP_LOCALE`.

This means `__()`, validation messages, dates and anything else that depends on the locale follow the URL automatically. With a Spanish translation in `lang/es.json`:

```php title="routes/web.php"
Route::get('/welcome', fn () => __('Welcome!'))
    ->lang(['es']);

// GET /welcome    → "Welcome!"
// GET /es/welcome → "¡Bienvenido!"
```

The locale is already set when route model binding runs, so your bindings can resolve translated slugs or other locale-dependent values.

> [!WARNING]
> The locale is set by a middleware the package adds to the `web` and `api` middleware groups. Routes defined in `routes/web.php` and `routes/api.php` belong to these groups by default. If you register localized routes outside of them, add the `Goodcat\L10n\Middleware\SetLocale` middleware yourself.

To read the locale of the current route, use `$request->route()->locale()`. See [Route helpers](helpers#route-helpers).

## Route names

Translated routes are named after the canonical route, followed by a dot and the locale:

```php
Route::get('/example', Controller::class)
    ->lang(['es', 'it'])
    ->name('example');

// Registered names: example, example.es, example.it
```

You rarely need these names directly. Call `route('example')` and the package picks the route for the current locale, or pass `lang` to choose another one. See [URL Generation](url-generation).

When checking the current route, use `L10n::is('example')` instead of `Route::is('example')`: it matches the canonical route and all its translations. See [Route matching](helpers#route-matching).

Routes without a name still get localized: the package assigns them, and their translations, an internal name starting with `generated::`.

## Route strategy

The `route_strategy` option in `config/l10n.php` controls how locale prefixes are applied. There are three strategies:

- `prefix_except_default` (default) keeps the fallback locale unprefixed and prefixes every other locale.
- `prefix` prefixes every locale, including the fallback one, and does not register an unprefixed route.
- `no_prefix` uses translated URIs without any locale prefix.

Here is how they compare for `Route::get('/example')->lang(['es', 'it'])`, with a Spanish translation for `example` and no Italian one:

| Locale | `prefix_except_default` | `prefix` | `no_prefix` |
| --- | --- | --- | --- |
| `en` (fallback) | `/example` | `/en/example` | `/example` |
| `es` | `/es/ejemplo` | `/es/ejemplo` | `/ejemplo` |
| `it` | `/it/example` | `/it/example` | not registered |

With `prefix`, requesting `/example` returns a 404: the fallback locale lives at `/en/example` like any other language. With `no_prefix`, the Italian route would have the same URI as the canonical one, so it is skipped (see *Collisions with no_prefix* in [Translating route URIs](#translating-route-uris)).

To change the strategy, publish the package config:

```bash
php artisan vendor:publish --tag=l10n-config
```

Then set the option in the published file:

```php title="config/l10n.php"
return [
    'route_strategy' => 'prefix',
];
```

The strategy applies to all localized routes of the application.

## Translating route URIs

Manage route translations in a `routes.php` file for each language, inside your `lang` directory:

```txt
/lang
├── /es
│   └── routes.php
├── /fr
│   └── routes.php
└── /it
    └── routes.php
```

Inside each file, map the original route URI to its translation:

```php title="lang/es/routes.php"
return [
    'example' => 'ejemplo',
    'products/{product}' => 'productos/{product}',
];
```

If no translation is provided for a locale, the original URI is used as-is.

> [!NOTE]
> The key is the route URI **without** the leading slash. For example, for `Route::get('/example')`, the key is `example`. Leading and trailing slashes in the translated value are ignored.

Keep the route parameters in the translated URI with the same names, so the translated route receives the same parameters as the canonical one. Only the static segments are meant to be translated.

### Custom binding keys

For routes with a custom binding key, omit the key (`:slug`) from the translation key and from the translated URI:

<!-- code-group -->

```php title="routes/web.php"
Route::get('/article/{post:slug}', [PostController::class, 'show'])
    ->lang(['it']);
```

```php title="lang/it/routes.php"
return [
    'article/{post}' => 'articolo/{post}',
];
```

<!-- /code-group -->

The localized route still binds `post` by its slug.

### Collisions with no_prefix

Without a locale prefix, two routes can end up with the same URI. With the `no_prefix` strategy, a translation that shares its domain and URI with the canonical route or with an earlier translation is skipped:

```php title="routes/web.php"
// lang/es/routes.php has no translation for 'dashboard'
Route::get('/dashboard', DashboardController::class)
    ->lang(['es'])
    ->name('dashboard');
```

Here `/dashboard` is served only by the canonical route, with the fallback locale. `route('dashboard', ['lang' => 'es'])` returns the canonical URL as well. To serve Spanish visitors on their own URL, add a translation for `dashboard` in `lang/es/routes.php`.

## Domain translations

If your application uses domain-based routing, you can translate domains in the same `routes.php` language files. The key is the original domain:

```php title="lang/es/routes.php"
return [
    'example' => 'ejemplo',
    'example.com' => 'es.example.com',
];
```

When a locale has a translated domain, its routes are not prefixed: the domain already identifies the language. Locales without a translated domain keep the path prefix on the original domain:

```php title="routes/web.php"
Route::domain('example.com')->lang(['es', 'it'])->group(function () {
    Route::get('/example', Controller::class);
});
```

| Locale | `prefix_except_default` | `prefix` |
| --- | --- | --- |
| `en` (fallback) | `example.com/example` | `example.com/en/example` |
| `es` (translated domain) | `es.example.com/ejemplo` | `es.example.com/ejemplo` |
| `it` | `example.com/it/example` | `example.com/it/example` |

With the `prefix` strategy, the fallback locale keeps its path prefix on the original domain, because there is no translated domain for it.

> [!NOTE]
> If you use translated domains together with [locale preference](locale-preference), configure a session shared across those domains.

## What translated routes inherit

A translated route is a copy of its canonical route, so it keeps everything you defined on it:

- the controller or closure;
- middleware, including the ones inherited from groups;
- parameter constraints defined with `where()` and default values defined with `defaults()`;
- route model binding options, such as custom keys and `withTrashed()`;
- `Route::fallback()` routes, whose translations are fallback routes as well.

You define the route once and every language behaves the same way, only with a different URL and locale.
