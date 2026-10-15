---
title: 'Guida rapida'
description: 'Installa laravel-l10n, localizza la tua prima route, traduci il suo URL e crea link verso di essa in ogni lingua.'
---

## Requisiti

- PHP 8.2 o superiore
- Laravel 12.44 o superiore, oppure Laravel 13

## Installazione

Installa il package con Composer:

```bash
composer require goodcat/laravel-l10n
```

Il package si registra da solo tramite la package discovery di Laravel: non c'è nessun service provider da aggiungere e, con le impostazioni predefinite, niente da configurare.

## Imposta il fallback locale

Il fallback locale è la lingua in cui sono scritti le tue route e i tuoi contenuti. Il package usa l'impostazione di Laravel, quindi controlla che sia corretta nel tuo `.env`:

```dotenv title=".env"
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

## Localizza una route

Aggiungi il metodo `lang()` a una route, elencando le altre lingue in cui deve essere disponibile:

```php title="routes/web.php"
Route::get('/about', AboutController::class)
    ->lang(['es', 'it'])
    ->name('about');
```

Non serve altro. La tua applicazione ora risponde a tre URL:

- `/about` in inglese, il fallback locale
- `/es/about` in spagnolo
- `/it/about` in italiano

Ognuno imposta il locale dell'applicazione sulla propria lingua, quindi `__()`, i messaggi di validazione e le date nel controller e nelle view vengono tradotti automaticamente.

## Traduci l'URL

`/es/about` funziona, ma i visitatori spagnoli preferirebbero leggere `/es/acerca-de`. Crea un file `routes.php` nella cartella della lingua e associa l'URI originale alla sua traduzione:

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

Ora le route sono `/about`, `/es/acerca-de` e `/it/chi-siamo`. Per controllarle, elenca le route registrate:

```bash
php artisan route:list --name=about
```

## Crea i link alla pagina

Genera i link con l'helper `route()`, esattamente come prima:

```blade title="resources/views/layouts/app.blade.php"
<a href="{{ route('about') }}">{{ __('About us') }}</a>
```

Il link segue la lingua della pagina corrente: punta a `/about` nelle pagine in inglese, a `/es/acerca-de` in quelle in spagnolo e a `/it/chi-siamo` in quelle in italiano. Per creare un link a una lingua specifica, passa `lang`:

```php
route('about', ['lang' => 'it']); // "https://example.com/it/chi-siamo"
```

## Permetti agli utenti di cambiare lingua

Aggiungi il selettore di lingua al tuo layout, così gli utenti possono passare da una versione all'altra della pagina corrente:

```blade title="resources/views/layouts/app.blade.php"
<x-l10n::switcher />
```

Mostra una `<select>` con le lingue disponibili per la route corrente e ricarica la pagina in quella scelta.

## E adesso?

Ora hai una pagina localizzata con URL tradotti. Da qui puoi:

- [Scoprire come funzionano le route localizzate](localized-routes), compresi i gruppi di route e i domini tradotti
- [Scegliere una strategia di routing](localized-routes#strategia-di-routing), per esempio per aggiungere il prefisso anche al fallback locale
- [Reindirizzare gli utenti alla loro lingua preferita](locale-preference) alla prima visita
- [Aggiungere i link hreflang](components#link-alternate-hreflang) per aiutare i motori di ricerca a trovare ogni versione delle tue pagine
