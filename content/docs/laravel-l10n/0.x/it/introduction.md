---
title: 'Laravel L10n'
description: 'Un package Laravel con le idee chiare sulla localizzazione delle applicazioni. Definisci route localizzate, traduci URI e domini, genera URL in qualsiasi lingua e rileva il locale preferito dall''utente.'
guides:
  - page: localized-routes
    description: 'Definisci route localizzate e traduci i loro URI e domini.'
  - page: url-generation
    description: 'Genera URL nella lingua corrente o in qualsiasi altra.'
  - page: locale-preference
    description: 'Rileva la lingua preferita dall’utente e reindirizzalo lì.'
  - page: javascript
    title: 'JavaScript'
    description: 'Genera URL localizzati con Wayfinder o Ziggy.'
features:
  - page: helpers
    description: 'Leggi i locale preferiti dall’utente, ottieni la lingua della route e confronta le route indipendentemente dal locale.'
  - page: components
    description: 'Mostra i link alternate hreflang e un selettore di lingua nei tuoi layout Blade.'
  - page: localized-views
    description: 'Organizza le view Blade specifiche per lingua in cartelle con il nome del locale.'
---

## In breve

Aggiungi `lang()` a una route ed elenca le lingue in cui deve essere disponibile. Se vuoi, traduci il suo URI nei file di lingua:

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

La tua applicazione ora serve la stessa pagina in tre lingue, ognuna con il proprio URL e il proprio locale dell'applicazione:

- `/products/42` in inglese
- `/es/productos/42` in spagnolo
- `/it/products/42` in italiano

I link continuano a funzionare come prima e seguono la lingua della pagina che l'utente sta leggendo:

```php
route('products.show', $product); // current language
route('products.show', [$product, 'lang' => 'es']); // "/es/productos/42"
```

## Costruito su Laravel

`laravel-l10n` aggiunge un solo metodo alle tue route e per tutto il resto si affida a quello che Laravel offre già:

- il **fallback locale** di `config/app.php` è la lingua delle tue route originali;
- i **file di lingua** in `lang/` contengono gli URI tradotti, accanto alle altre traduzioni;
- gli **helper `route()` e `action()`**, i redirect e gli URL firmati generano URL localizzati senza nuove API;
- **cache delle route**, middleware, route model binding e gruppi di route funzionano come sempre sulle route tradotte;
- l'interfaccia **`HasLocalePreference`**, già usata da notifiche e mailable, fornisce la lingua preferita dall'utente.

Se conosci il routing di Laravel, conosci già buona parte di `laravel-l10n`.
