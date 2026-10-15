---
title: 'Helpers'
description: 'Helper methods added by laravel-l10n to the application and to routes, plus locale-independent route matching.'
---

## Application helpers

```php
// Get the user's preferred locales, in order of preference
app()->getPreferredLocales(); // Returns ?array, e.g. ['es_ES', 'es', 'en']

// Get the user's first preferred locale
app()->getPreferredLocale(); // Returns ?string, e.g. 'es_ES'

// Get the best match among the given locales, or null
app()->getPreferredLocale(['en', 'es']); // Returns ?string, e.g. 'es'

// Set the user's preferred locales (dispatches PreferredLocaleUpdated event)
app()->setPreferredLocale('es');
app()->setPreferredLocale(['es_ES', 'es', 'en']);

// Check if a locale is the fallback locale
app()->isFallbackLocale('en'); // Returns bool
```

When given a list of locales, `getPreferredLocale()` follows the preferences in order and, for each one, looks for an exact match, then its language (`es_ES` matches `es`), then the first variant of the same language in the given list (`es_ES` matches `es_MX`). It returns `null` when nothing matches, so you can provide a default explicitly:

```php
app()->getPreferredLocale(['en', 'es']) ?? 'en';
```

## Route helpers

```php
// Get the route's language, or the fallback locale for a non-localized route
$request->route()->locale(); // Returns string
```

## Route matching

Use `L10n::is()` to check if the current route matches a given pattern, regardless of the locale:

```php
L10n::is('dashboard');  // Matches /dashboard, /es/dashboard, /it/bacheca, etc.
L10n::is('admin.*');    // Wildcard patterns are supported, just like Route::is()
```

Use the original route name in your patterns, without a locale suffix.
