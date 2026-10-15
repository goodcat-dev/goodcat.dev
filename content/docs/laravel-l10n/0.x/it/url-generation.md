---
title: 'Generazione degli URL'
description: 'Genera URL localizzati con gli helper route() e action(), e metti in cache le route localizzate.'
---

## L'helper route()

Chiama `route()` con il nome della route canonica, come faresti senza il package. L'URL segue il locale corrente dell'applicazione:

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

Dato che le route localizzate [impostano il locale dell'applicazione](localized-routes#il-locale-dellapplicazione), i link restano nella lingua della pagina che l'utente sta leggendo, senza codice aggiuntivo nelle view:

```blade
<a href="{{ route('example') }}">Example</a>
```

### Link a un'altra lingua

Passa il parametro `lang` per generare l'URL di un locale specifico:

```php
route('example', ['lang' => 'es']); // "https://example.com/es/ejemplo"
route('example', ['lang' => 'it']); // "https://example.com/it/example"
route('example', ['lang' => 'en']); // "https://example.com/example"
```

`lang` serve solo a scegliere la lingua: viene rimosso dai parametri, quindi non finisce mai nella query string. Combinalo con gli altri parametri della route come sempre:

```php
route('products.show', ['product' => $product, 'lang' => 'es']);
// "https://example.com/es/productos/42"
```

### Regole di risoluzione

Il locale dell'URL generato viene scelto così:

1. Il parametro `lang`, se presente.
2. Il locale della route stessa, quando passi il nome di una route tradotta come `example.it`.
3. Altrimenti, il locale corrente dell'applicazione.

Se la route non ha una traduzione per il locale scelto, `route()` restituisce l'URL della route che hai indicato invece di generare un errore:

| Chiamata | Risultato |
| --- | --- |
| `route('example')` con locale dell'app `es` | `/es/ejemplo` |
| `route('example')` con locale dell'app `de` | `/example` |
| `route('example', ['lang' => 'de'])` | `/example` |
| `route('example.it')` con locale dell'app `es` | `/it/example` |
| `route('example.it', ['lang' => 'es'])` | `/es/ejemplo` |
| `route('example.it', ['lang' => 'de'])` | `/it/example` |

Gli esempi usano la [strategia](localized-routes#strategia-di-routing) predefinita `prefix_except_default`: con `prefix`, l'URL canonico è invece `/en/example`.

Le route senza `lang()` non cambiano: `route()` restituisce il loro URL di sempre e ignora `lang`.

> [!WARNING]
> `lang` è riservato alla scelta della lingua dell'URL. Usa un nome diverso per i parametri delle route: gli URL che contengono `{lang}` non possono essere generati con `route()` o `action()`.

## L'helper action()

L'helper `action()` segue le stesse regole, con il controller al posto del nome della route:

```php
action(Controller::class); // URL in the current locale
action(Controller::class, ['lang' => 'es']); // "https://example.com/es/ejemplo"
action([ProductController::class, 'show'], ['product' => $product, 'lang' => 'es']);
```

## Link alla pagina corrente in un'altra lingua

Per costruire un menu delle lingue, genera l'URL della route corrente per ogni locale, passando i suoi parametri:

```php
$route = request()->route();

route($route->getName(), [...$route->parameters(), 'lang' => 'es']);
```

Funziona sia dalla route canonica sia da una qualsiasi delle sue traduzioni. Nella maggior parte dei casi, i componenti [selettore di lingua](components#selettore-di-lingua) e [link alternate](components#link-alternate-hreflang) lo fanno già per te.

## Redirect e URL firmati

Tutto ciò che si basa sull'URL generator di Laravel viene localizzato allo stesso modo, compresi redirect e URL firmati:

```php
return redirect()->route('example', ['lang' => 'es']);

return to_route('example'); // current locale

URL::temporarySignedRoute('example', now()->addMinutes(30), ['lang' => 'es']);
// "https://example.com/es/ejemplo?expires=...&signature=..."
```

Gli URL firmati mantengono una firma valida sulle route tradotte.

## Cache delle route

Le route localizzate supportano la cache standard delle route di Laravel. Le route tradotte vengono salvate in cache insieme a quelle canoniche:

```bash
php artisan route:cache
```

Dopo aver modificato la traduzione di una route in `lang/*/routes.php`, svuota o ricostruisci la cache come faresti dopo aver modificato una route.
