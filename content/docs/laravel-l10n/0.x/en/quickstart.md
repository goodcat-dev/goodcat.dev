---
title: 'Quickstart'
description: 'Install laravel-l10n, localize your first route, translate its URL and link to it in every language.'
---

## Requirements

- PHP 8.2 or higher
- Laravel 12.44 or higher, or Laravel 13

## Installation

Install the package via Composer:

```bash
composer require goodcat/laravel-l10n
```

The package registers itself through Laravel's package discovery: there is no service provider to add and, with the defaults, nothing to configure.

## Set the fallback locale

The fallback locale is the language your routes and content are written in. The package uses Laravel's own setting, so check that it matches in your `.env`:

```dotenv title=".env"
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

## Localize a route

Add the `lang()` method to a route, listing the other languages it should be available in:

```php title="routes/web.php"
Route::get('/about', AboutController::class)
    ->lang(['es', 'it'])
    ->name('about');
```

That's all it takes. Your application now responds to three URLs:

- `/about` in English, the fallback locale
- `/es/about` in Spanish
- `/it/about` in Italian

Each of them sets the application locale to its own language, so `__()`, validation messages and dates in your controller and views are translated automatically.

## Translate the URL

`/es/about` works, but Spanish visitors would rather read `/es/acerca-de`. Create a `routes.php` file in the language directory and map the original URI to its translation:

```php title="lang/es/routes.php"
return [
    'about' => 'acerca-de',
];
```

```php title="lang/it/routes.php"
return [
    'about' => 'chi-siamo',
];
```

The routes are now `/about`, `/es/acerca-de` and `/it/chi-siamo`. To check them, list the registered routes:

```bash
php artisan route:list --name=about
```

## Link to the page

Generate links with the `route()` helper, exactly as before:

```blade title="resources/views/layouts/app.blade.php"
<a href="{{ route('about') }}">{{ __('About us') }}</a>
```

The link follows the language of the current page: it points to `/about` on English pages, to `/es/acerca-de` on Spanish pages and to `/it/chi-siamo` on Italian pages. To link to a specific language, pass `lang`:

```php
route('about', ['lang' => 'it']); // "https://example.com/it/chi-siamo"
```

## Let users switch language

Add the locale switcher to your layout, so users can move between the versions of the current page:

```blade title="resources/views/layouts/app.blade.php"
<x-l10n::switcher />
```

It renders a `<select>` with the languages available for the current route, and reloads the page in the chosen one.

## What's next?

You now have a localized page with translated URLs. From here, you can:

- [Learn how localized routes work](localized-routes), including route groups and translated domains
- [Choose a route strategy](localized-routes#route-strategy), for example to prefix the fallback locale too
- [Redirect users to their preferred language](locale-preference) on their first visit
- [Add hreflang links](components#alternate-hreflang-links) to help search engines find every version of your pages
