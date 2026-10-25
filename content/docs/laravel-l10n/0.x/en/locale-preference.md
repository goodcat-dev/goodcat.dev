---
title: 'Locale Preference'
description: 'Detect the user’s preferred language, redirect to it on the first visit, and customize how the preference is resolved.'
---

## Active and preferred locale

`laravel-l10n` keeps two separate concepts:

- The **active locale** is the language the current page is served in. It comes from the URL: `/es/ejemplo` is served in Spanish, whatever the user prefers. You read it with `app()->getLocale()`.
- The **preferred locale** is the language the user would like to read. It comes from the browser, the user's profile or any other source you choose. You read it with `app()->getPreferredLocale()`.

Detecting the preferred locale never changes the active locale. A Spanish speaker who opens `/example` still gets the English page: the URL decides. What you do with the preference is up to you: redirect the user, show a banner suggesting their language, or simply ignore it.

## Detecting the preferred locale

To detect the user's preferred language, add the `SetPreferredLocale` middleware to the `web` group in `bootstrap/app.php`:

```php title="bootstrap/app.php"
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \Goodcat\L10n\Middleware\SetPreferredLocale::class,
    ]);
})
```

On every request, the middleware asks a list of **resolvers** for the user's preference, in order. The first resolver that returns a value wins, and the remaining ones are skipped.

By default, the package checks the following sources in order:

1. **SessionLocale**: the `locale` key of the session. This resolver is deprecated: see *Session-based preferences* in [Creating a custom resolver](#creating-a-custom-resolver).
2. **UserLocale**: the preferred locale of the authenticated user.
3. **BrowserLocale**: the languages of the browser's `Accept-Language` header, all of them, in order of preference.

Once resolved, the preference is available anywhere in your application for the rest of the request:

```php
app()->getPreferredLocales(); // ['es_ES', 'es', 'en']
app()->getPreferredLocale();  // 'es_ES'
```

### The user's preferred locale

`UserLocale` reads the preference from the authenticated user, when the user model implements Laravel's `HasLocalePreference` interface:

```php title="app/Models/User.php"
use Illuminate\Contracts\Translation\HasLocalePreference;

class User extends Authenticatable implements HasLocalePreference
{
    public function preferredLocale(): ?string
    {
        return $this->locale;
    }
}
```

Laravel uses the same interface to pick the language of notifications and mailables sent to the user, so a single `locale` column serves both purposes. If the user has no preference stored, the resolver returns `null` and the next one is checked.

### Matching the available locales

The browser usually sends regional locales, such as `es_ES` or `pt_BR`, while your routes may only exist for `es` and `pt`. Pass the list of locales you support to `getPreferredLocale()` to get the best match:

```php
// The browser sends: Accept-Language: es-ES,es;q=0.9,en;q=0.8
app()->getPreferredLocale(['en', 'es', 'it']); // 'es'
```

For each preferred locale, in order, the method looks for an exact match, then for its language (`es_ES` matches `es`), then for another variant of the same language (`es_ES` matches `es_MX`). It returns `null` when nothing matches. See [Application helpers](helpers#application-helpers) for the full list of methods.

## Redirecting to the preferred locale

To send users to their language automatically, add `RedirectToPreferredLocale` after `SetPreferredLocale`:

```php title="bootstrap/app.php"
$middleware->web(append: [
    \Goodcat\L10n\Middleware\SetPreferredLocale::class,
    \Goodcat\L10n\Middleware\RedirectToPreferredLocale::class,
]);
```

On the first GET request to a route with translations, the middleware compares the user's preference with the languages of that route. If a better match exists and differs from the current page, the user is redirected to it with an HTTP 302. Route parameters and the query string are preserved:

```txt
Accept-Language: es-ES,es;q=0.9

GET /products/42?color=red
→ 302 /es/productos/42?color=red
```

The check runs **once per session**, even when no redirect is needed. After the first localized page, users can switch language freely, for example with the [locale switcher](components#locale-switcher), without being sent back.

Only requests that can be redirected count as the first visit:

- non-GET requests, such as form submissions, are never redirected;
- routes without translations are ignored, so the check happens on the first localized page the user opens;
- locales without a [localized route key](localized-routes#translated-route-keys) for the page's models are not considered as a destination.

> [!NOTE]
> This feature requires a session, which Laravel's `web` group provides. When using [translated domains](localized-routes#domain-translations), configure a session shared across those domains.

## Customizing resolvers

To choose which sources to check and in what order, set the resolvers in the `boot()` method of your `AppServiceProvider`:

```php title="app/Providers/AppServiceProvider.php"
use Goodcat\L10n\L10n;
use Goodcat\L10n\Resolvers\BrowserLocale;
use Goodcat\L10n\Resolvers\UserLocale;

public function boot(): void
{
    L10n::$preferredLocaleResolvers = [
        new UserLocale,
        new BrowserLocale,
    ];
}
```

This example also drops the deprecated `SessionLocale` resolver from the defaults.

## Creating a custom resolver

Implement the `LocaleResolver` interface to read the preference from any other source. The `resolve()` method returns a locale, a list of locales in order of preference, or `null` to let the next resolver try:

```php title="app/Resolvers/CookieLocale.php"
namespace App\Resolvers;

use Goodcat\L10n\Resolvers\LocaleResolver;
use Illuminate\Http\Request;

class CookieLocale implements LocaleResolver
{
    public function resolve(Request $request): ?string
    {
        return $request->cookie('locale');
    }
}
```

Then add it to the resolvers in your `AppServiceProvider::boot()` method. Your application is responsible for setting the cookie, for example when the user picks a language:

```php title="app/Providers/AppServiceProvider.php"
use App\Resolvers\CookieLocale;
use Goodcat\L10n\L10n;
use Goodcat\L10n\Resolvers\BrowserLocale;
use Goodcat\L10n\Resolvers\UserLocale;

public function boot(): void
{
    L10n::$preferredLocaleResolvers = [
        new CookieLocale,
        new UserLocale,
        new BrowserLocale,
    ];
}
```

### Session-based preferences

The built-in `SessionLocale` resolver is deprecated. If you store the preference in the session, write a resolver for it, so you control the session key:

```php title="app/Resolvers/SessionLocale.php"
namespace App\Resolvers;

use Goodcat\L10n\Resolvers\LocaleResolver;
use Illuminate\Http\Request;

class SessionLocale implements LocaleResolver
{
    public function resolve(Request $request): ?string
    {
        return $request->session()->get('locale');
    }
}
```

## The PreferredLocaleUpdated event

Every time the preferred locale is set, by the middleware or by your own call to `app()->setPreferredLocale()`, the package dispatches a `PreferredLocaleUpdated` event. It works like Laravel's `LocaleUpdated` event, which is dispatched by `app()->setLocale()`: it carries the new locales and the ones set before in the same request, if any:

```php title="app/Listeners/HandlePreferredLocaleUpdated.php"
namespace App\Listeners;

use Goodcat\L10n\Events\PreferredLocaleUpdated;

class HandlePreferredLocaleUpdated
{
    public function handle(PreferredLocaleUpdated $event): void
    {
        $event->locales; // ['es_ES', 'es']
        $event->previousLocales; // null on the first update of the request
    }
}
```

Like `LocaleUpdated`, the event is dispatched even when the value does not change. The preference is resolved again on every request, so with `SetPreferredLocale` in the `web` group the event is dispatched on every request where a resolver returns a value. Since no preference is set by default, `previousLocales` is `null` the first time.
