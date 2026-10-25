---
title: 'Guida all''aggiornamento'
description: 'Aggiorna dalla versione 1.x alla 2.x: cambia il nome del package, l’API resta la stessa.'
---

## Aggiornare dalla 1.x

La versione 2.0 non contiene modifiche incompatibili all'API: filtri, scope `queryString()`, oggetti filtro e cache funzionano come prima. La major indica il nuovo nome del package e il supporto a Laravel 13.

### Nome del package

Il package ora è pubblicato come `goodcat/laravel-eloquent-querystring`. Sostituisci il vecchio nome nel tuo `composer.json`, che sia `goodcat/laravel-querystring` o `goodcat/laravel-filter-querystring`:

```json title="composer.json"
"require": {
    "goodcat/laravel-eloquent-querystring": "^2.2"
}
```

Poi aggiorna le dipendenze:

```bash
composer update goodcat/laravel-eloquent-querystring
```

Il namespace PHP resta `Goodcat\QueryString`, quindi il tuo codice non richiede modifiche.

### Requisiti

La versione 2.x supporta Laravel 11, 12 e 13 e richiede PHP 8.2 o superiore, come la 1.x.

### Cache

Rigenera la cache dei filtri dopo l'aggiornamento:

```bash
php artisan querystring:cache
```

## Novità della 2.x

- [Più nomi in un solo attributo](filter-methods#nomi-dei-parametri), come `#[QueryString('name', 'email')]`, dalla 2.1.
- [Filtri dal body delle richieste HTTP QUERY](query-requests), dalla 2.2.
- [I model in cache](caching#quali-model-finiscono-in-cache) includono quelli che ereditano il trait da una classe padre o da un altro trait, dalla 2.2.1.
