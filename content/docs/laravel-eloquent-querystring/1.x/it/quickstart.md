---
title: 'Guida rapida'
description: 'Installa laravel-eloquent-querystring, scrivi il tuo primo filtro e applicalo a una query.'
---

## Requisiti

- PHP 8.2 o superiore
- Laravel 11 o 12

## Installazione

Installa il package con Composer:

```bash
composer require goodcat/laravel-eloquent-querystring:^1.1
```

Le prime release erano pubblicate come `goodcat/laravel-querystring`. Quel nome non è più disponibile su Packagist: richiedi il package con il suo nome attuale.

Il package si registra da solo tramite la package discovery di Laravel: non c'è nessun service provider da aggiungere e, con le impostazioni predefinite, niente da configurare.

## Aggiungi il trait

Usa il trait `UseQueryString` nel model che vuoi filtrare:

```php title="app/Models/User.php"
use Goodcat\QueryString\Traits\UseQueryString;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use UseQueryString;
}
```

Il trait aggiunge lo scope `queryString()` al model.

## Scrivi un filtro

Un filtro è un metodo pubblico marcato con l'attributo `QueryString`. L'argomento dell'attributo è il nome del parametro della query string, e il metodo riceve il query builder e il valore del parametro:

```php title="app/Models/User.php"
use Goodcat\QueryString\Attributes\QueryString;
use Goodcat\QueryString\Traits\UseQueryString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use UseQueryString;

    #[QueryString('email')]
    public function filterByEmail(Builder $query, string $email): void
    {
        $query->where('email', $email);
    }
}
```

Il nome del metodo è a tua scelta: conta solo l'attributo.

## Applica i filtri

Chiama lo scope `queryString()` con la richiesta corrente:

```php title="routes/api.php"
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/users', function (Request $request) {
    return User::query()->queryString($request)->get();
});
```

Richiedi `/api/users?email=john%40doe.com` e la query restituisce solo gli utenti con quella email. Senza il parametro il filtro non viene eseguito e vengono restituiti tutti gli utenti.

## Prepara la produzione

Il package trova i tuoi filtri leggendo gli attributi dei model. In produzione mettili in cache insieme al resto dell'applicazione:

```bash
php artisan optimize
```

Ricordati di eseguirlo di nuovo a ogni deploy. Trovi i dettagli in [Cache](caching).

## E adesso?

Ora hai un model che si filtra da solo a partire dalla richiesta. Da qui puoi:

- [Associare più parametri a un filtro](filter-methods#nomi-dei-parametri) e leggere il nome del parametro
- [Filtrare con dati validati](applying-filters#passare-un-array) invece che con la richiesta grezza
- [Spostare i filtri in una classe separata](filter-objects)
- [Mettere in cache i filtri](caching) e capire quando rigenerarli
