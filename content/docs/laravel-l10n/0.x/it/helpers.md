---
title: 'Helper'
description: 'I metodi helper che laravel-l10n aggiunge all''applicazione e alle route, più la corrispondenza delle route indipendente dal locale.'
---

## Helper dell'applicazione

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

Quando riceve un elenco di locale, `getPreferredLocale()` segue le preferenze in ordine e, per ognuna, cerca una corrispondenza esatta, poi la sua lingua (`es_ES` corrisponde a `es`), poi la prima variante della stessa lingua nell'elenco (`es_ES` corrisponde a `es_MX`). Restituisce `null` quando non trova nulla, così puoi indicare esplicitamente un valore predefinito:

```php
app()->getPreferredLocale(['en', 'es']) ?? 'en';
```

## Helper delle route

```php
// Get the route's language, or the fallback locale for a non-localized route
$request->route()->locale(); // Returns string

// Check if the route is a canonical route with translations
$request->route()->isCanonical(); // Returns bool

// Check if the route is the translation of a canonical route
$request->route()->isTranslation(); // Returns bool

// Get the registered route for a locale, or null
$request->route()->getTranslation('es'); // Returns ?Route
```

Le route senza `lang()` non sono né canoniche né traduzioni. `getTranslation()` risponde sia dalla route canonica sia da una qualsiasi delle sue traduzioni, e per il fallback locale restituisce la route canonica.

## Corrispondenza delle route

Usa `L10n::is()` per controllare se la route corrente corrisponde a un pattern, indipendentemente dal locale:

```php
L10n::is('dashboard');  // Matches /dashboard, /es/dashboard, /it/bacheca, etc.
L10n::is('admin.*');    // Wildcard patterns are supported, just like Route::is()
```

Nei pattern usa il nome originale della route, senza il suffisso del locale.
