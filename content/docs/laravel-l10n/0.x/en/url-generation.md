---
title: 'URL Generation'
description: 'Generate localized URLs with the route() and action() helpers, and cache localized routes.'
---

## The route() helper

Call `route()` with the name of the canonical route, as you would without the package. The URL follows the current application locale:

```php title="routes/web.php"
Route::get('/example', Controller::class)
    ->lang(['es', 'it'])
    ->name('example');
```

```php
// On a page served in Spanish, such as /es/ejemplo
route('example'); // "https://example.com/es/ejemplo"

// On a page served in the fallback locale, such as /example
route('example'); // "https://example.com/example"
```

Since localized routes [set the application locale](localized-routes#the-application-locale), links stay in the language of the page the user is reading, without any extra code in your views:

```blade
<a href="{{ route('example') }}">Example</a>
```

### Linking to another language

Pass the `lang` parameter to generate the URL for a specific locale:

```php
route('example', ['lang' => 'es']); // "https://example.com/es/ejemplo"
route('example', ['lang' => 'it']); // "https://example.com/it/example"
route('example', ['lang' => 'en']); // "https://example.com/example"
```

`lang` only selects the language: it is removed from the parameters, so it never ends up in the query string. Combine it with the other route parameters as usual:

```php
route('products.show', ['product' => $product, 'lang' => 'es']);
// "https://example.com/es/productos/42"
```

### Resolution rules

The locale of the generated URL is chosen as follows:

1. The `lang` parameter, when given.
2. The locale of the route itself, when you pass the name of a translated route such as `example.it`.
3. Otherwise, the current application locale.

If the route has no translation for the chosen locale, `route()` returns the URL of the route you named instead of failing:

| Call | Result |
| --- | --- |
| `route('example')` with app locale `es` | `/es/ejemplo` |
| `route('example')` with app locale `de` | `/example` |
| `route('example', ['lang' => 'de'])` | `/example` |
| `route('example.it')` with app locale `es` | `/it/example` |
| `route('example.it', ['lang' => 'es'])` | `/es/ejemplo` |
| `route('example.it', ['lang' => 'de'])` | `/it/example` |

The examples use the default `prefix_except_default` [strategy](localized-routes#route-strategy): with `prefix`, the canonical URL is `/en/example` instead.

Routes without `lang()` are not affected: `lang` is a regular parameter for them, so it fills a `{lang}` segment or ends up in the query string, as it would without the package.

> [!WARNING]
> On localized routes, `lang` is reserved for selecting the URL's language. Use a different name for their route parameters: a localized route with a `{lang}` segment cannot be generated with `route()` or `action()`.

## The action() helper

The `action()` helper follows the same rules, with the controller instead of the route name:

```php
action(Controller::class); // URL in the current locale
action(Controller::class, ['lang' => 'es']); // "https://example.com/es/ejemplo"
action([ProductController::class, 'show'], ['product' => $product, 'lang' => 'es']);
```

## Missing localized route keys

When a model has no [localized route key](localized-routes#translated-route-keys) for a locale, `route()` and `action()` fall back to the canonical route, unless the locale is requested explicitly:

```php
// The post has no Italian slug and the application locale is "it"
route('posts.show', $post); // "https://example.com/article/hello-world"

route('posts.show', ['post' => $post, 'lang' => 'it']); // Throws MissingLocalizedRouteKeyException
route('posts.show.it', $post); // Throws MissingLocalizedRouteKeyException
```

`MissingLocalizedRouteKeyException` extends Laravel's `UrlGenerationException`. Catch it to skip the locale, for example in a sitemap:

```php
use Goodcat\L10n\Routing\Exceptions\MissingLocalizedRouteKeyException;

try {
    $url = route('posts.show', ['post' => $post, 'lang' => 'it']);
} catch (MissingLocalizedRouteKeyException) {
    // The post has no Italian slug
}
```

The canonical route key is always required: when it is missing, `MissingLocalizedRouteKeyException` is thrown for the canonical route as well.

## Linking to the current page in another language

To build a language menu, generate the URL of the current route for each locale, passing along its parameters:

```php
$route = request()->route();

route($route->getName(), [...$route->parameters(), 'lang' => 'es']);
```

This works from the canonical route and from any of its translations. For most cases, the [locale switcher](components#locale-switcher) and the [alternate links](components#alternate-hreflang-links) components already do this for you.

## Redirects and signed URLs

Everything built on top of Laravel's URL generator is localized the same way, including redirects and signed URLs:

```php
return redirect()->route('example', ['lang' => 'es']);

return to_route('example'); // current locale

URL::temporarySignedRoute('example', now()->addMinutes(30), ['lang' => 'es']);
// "https://example.com/es/ejemplo?expires=...&signature=..."
```

Signed URLs keep a valid signature on translated routes.

## Route caching

Localized routes support Laravel's standard route caching. Translated routes are stored in the cache together with the canonical ones:

```bash
php artisan route:cache
```

After changing a route translation in `lang/*/routes.php`, clear or rebuild the cache like you would after changing a route.
