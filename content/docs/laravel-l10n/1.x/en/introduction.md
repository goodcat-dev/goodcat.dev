---
title: 'Laravel L10n'
description: 'An opinionated Laravel package for app localization. Define localized routes, translate their URIs and domains, generate URLs in any language, and detect the user''s preferred locale.'
guides:
  - page: localized-routes
    description: 'Define localized routes and translate their URIs and domains.'
  - page: url-generation
    description: 'Generate URLs in the current language or in any other one.'
  - page: locale-preference
    description: 'Detect the user’s preferred language and redirect to it.'
  - page: javascript
    title: 'JavaScript'
    description: 'Generate localized URLs with Wayfinder or Ziggy.'
features:
  - page: helpers
    description: 'Read the user’s preferred locales, get the route’s language, and match routes regardless of the locale.'
  - page: components
    description: 'Render alternate hreflang links and a locale switcher in your Blade layouts.'
  - page: localized-views
    description: 'Organize language-specific Blade views in folders named after their locale.'
---

## At a glance

Add `lang()` to a route and list the languages it should be available in. Translate its URI in your language files, if you want to:

<!-- code-group -->

```php title="routes/web.php"
Route::get('/products/{product}', [ProductController::class, 'show'])
    ->lang(['es', 'it'])
    ->name('products.show');
```

```php title="lang/es/routes.php"
return [
    'products/{product}' => 'productos/{product}',
];
```

<!-- /code-group -->

Your application now serves the same page in three languages, each one with its own URL and its own application locale:

- `/products/42` in English
- `/es/productos/42` in Spanish
- `/it/products/42` in Italian

Links keep working as before, and follow the language of the page the user is reading:

```php
route('products.show', $product); // current language
route('products.show', [$product, 'lang' => 'es']); // "/es/productos/42"
```

## Built on Laravel

`laravel-l10n` adds a single method to your routes and relies on what Laravel already provides for everything else:

- the **fallback locale** from `config/app.php` is the language of your original routes;
- **language files** in `lang/` hold the translated URIs, next to your other translations;
- the **`route()` and `action()` helpers**, redirects and signed URLs generate localized URLs without new APIs;
- **route caching**, middleware, route model binding and route groups work as usual on translated routes;
- the **`HasLocalePreference`** interface, already used by notifications and mailables, provides the user's preferred language.

If you know Laravel's routing, you already know most of `laravel-l10n`.
