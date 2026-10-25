---
title: 'Lingua preferita'
description: 'Rileva la lingua preferita dall’utente, reindirizzalo lì alla prima visita e personalizza come viene determinata la preferenza.'
---

## Locale attivo e locale preferito

`laravel-l10n` tiene separati due concetti:

- Il **locale attivo** è la lingua in cui viene servita la pagina corrente. Dipende dall'URL: `/es/ejemplo` è servita in spagnolo, qualunque sia la preferenza dell'utente. Lo leggi con `app()->getLocale()`.
- Il **locale preferito** è la lingua in cui l'utente vorrebbe leggere. Viene dal browser, dal profilo dell'utente o da qualsiasi altra fonte tu scelga. Lo leggi con `app()->getPreferredLocale()`.

Rilevare il locale preferito non cambia mai il locale attivo. Un utente di lingua spagnola che apre `/example` riceve comunque la pagina in inglese: decide l'URL. Cosa fare con la preferenza sta a te: reindirizzare l'utente, mostrare un banner che suggerisce la sua lingua o semplicemente ignorarla.

## Rilevare il locale preferito

Per rilevare la lingua preferita dall'utente, aggiungi il middleware `SetPreferredLocale` al gruppo `web` in `bootstrap/app.php`:

```php title="bootstrap/app.php"
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \Goodcat\L10n\Middleware\SetPreferredLocale::class,
    ]);
})
```

A ogni richiesta, il middleware chiede la preferenza dell'utente a un elenco di **resolver**, in ordine. Vince il primo resolver che restituisce un valore, e gli altri vengono saltati.

Per impostazione predefinita, il package controlla queste fonti in ordine:

1. **SessionLocale**: la chiave `locale` della sessione. Questo resolver è deprecato: vedi *Preferenze salvate in sessione* in [Creare un resolver personalizzato](#creare-un-resolver-personalizzato).
2. **UserLocale**: il locale preferito dell'utente autenticato.
3. **BrowserLocale**: le lingue dell'header `Accept-Language` del browser, tutte, in ordine di preferenza.

Una volta determinata, la preferenza è disponibile ovunque nell'applicazione per il resto della richiesta:

```php
app()->getPreferredLocales(); // ['es_ES', 'es', 'en']
app()->getPreferredLocale();  // 'es_ES'
```

### Il locale preferito dell'utente

`UserLocale` legge la preferenza dall'utente autenticato, quando il model dell'utente implementa l'interfaccia `HasLocalePreference` di Laravel:

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

Laravel usa la stessa interfaccia per scegliere la lingua delle notifiche e dei mailable inviati all'utente, quindi una sola colonna `locale` serve a entrambi gli scopi. Se l'utente non ha una preferenza salvata, il resolver restituisce `null` e si passa al successivo.

### Corrispondenza con i locale disponibili

Di solito il browser invia locale regionali, come `es_ES` o `pt_BR`, mentre le tue route potrebbero esistere solo per `es` e `pt`. Passa a `getPreferredLocale()` l'elenco dei locale che supporti per ottenere la corrispondenza migliore:

```php
// The browser sends: Accept-Language: es-ES,es;q=0.9,en;q=0.8
app()->getPreferredLocale(['en', 'es', 'it']); // 'es'
```

Per ogni locale preferito, in ordine, il metodo cerca una corrispondenza esatta, poi la sua lingua (`es_ES` corrisponde a `es`), poi un'altra variante della stessa lingua (`es_ES` corrisponde a `es_MX`). Restituisce `null` quando non trova nulla. Vedi [Helper dell'applicazione](helpers#helper-dellapplicazione) per l'elenco completo dei metodi.

## Redirect al locale preferito

Per portare automaticamente gli utenti nella loro lingua, aggiungi `RedirectToPreferredLocale` dopo `SetPreferredLocale`:

```php title="bootstrap/app.php"
$middleware->web(append: [
    \Goodcat\L10n\Middleware\SetPreferredLocale::class,
    \Goodcat\L10n\Middleware\RedirectToPreferredLocale::class,
]);
```

Alla prima richiesta GET verso una route con traduzioni, il middleware confronta la preferenza dell'utente con le lingue di quella route. Se esiste una corrispondenza migliore e diversa dalla pagina corrente, l'utente viene reindirizzato lì con un HTTP 302. I parametri della route e la query string vengono mantenuti:

```txt
Accept-Language: es-ES,es;q=0.9

GET /products/42?color=red
→ 302 /es/productos/42?color=red
```

Il controllo avviene **una volta per sessione**, anche quando non serve nessun redirect. Dopo la prima pagina localizzata, gli utenti possono cambiare lingua liberamente, per esempio con il [selettore di lingua](components#selettore-di-lingua), senza essere riportati indietro.

Contano come prima visita solo le richieste che possono essere reindirizzate:

- le richieste non GET, come l'invio di un form, non vengono mai reindirizzate;
- le route senza traduzioni vengono ignorate, quindi il controllo avviene sulla prima pagina localizzata che l'utente apre;
- i locale senza una [chiave di route localizzata](localized-routes#chiavi-di-route-tradotte) per i model della pagina non vengono considerati come destinazione.

> [!NOTE]
> Questa funzionalità richiede una sessione, che il gruppo `web` di Laravel fornisce. Se usi i [domini tradotti](localized-routes#traduzione-dei-domini), configura una sessione condivisa tra quei domini.

## Personalizzare i resolver

Per scegliere quali fonti controllare e in che ordine, imposta i resolver nel metodo `boot()` del tuo `AppServiceProvider`:

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

Questo esempio toglie anche il resolver deprecato `SessionLocale` da quelli predefiniti.

## Creare un resolver personalizzato

Implementa l'interfaccia `LocaleResolver` per leggere la preferenza da qualsiasi altra fonte. Il metodo `resolve()` restituisce un locale, un elenco di locale in ordine di preferenza, oppure `null` per lasciar provare il resolver successivo:

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

Poi aggiungilo ai resolver nel metodo `AppServiceProvider::boot()`. È la tua applicazione a dover impostare il cookie, per esempio quando l'utente sceglie una lingua:

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

### Preferenze salvate in sessione

Il resolver `SessionLocale` incluso nel package è deprecato. Se salvi la preferenza in sessione, scrivi un resolver apposito, così controlli tu la chiave di sessione:

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

## L'evento PreferredLocaleUpdated

Ogni volta che il locale preferito viene impostato, dal middleware o da una tua chiamata a `app()->setPreferredLocale()`, il package lancia un evento `PreferredLocaleUpdated`. Funziona come l'evento `LocaleUpdated` di Laravel, lanciato da `app()->setLocale()`: contiene i nuovi locale e quelli impostati in precedenza nella stessa richiesta, se ce ne sono:

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

Come `LocaleUpdated`, l'evento viene lanciato anche quando il valore non cambia. La preferenza viene determinata di nuovo a ogni richiesta, quindi con `SetPreferredLocale` nel gruppo `web` l'evento viene lanciato a ogni richiesta in cui un resolver restituisce un valore. Dato che per impostazione predefinita non c'è nessuna preferenza, `previousLocales` è `null` la prima volta.
