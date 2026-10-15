---
title: 'Route localizzate'
description: 'Definisci i locale supportati da una route, scegli come applicare i prefissi di lingua e traduci URI e domini delle route.'
---

## Come funziona

Ogni route che marchi con `lang()` diventa la **route canonica**: mantiene il suo URI e il suo nome, e serve il fallback locale. Per ogni locale aggiuntivo, il package registra una **route tradotta**, una copia della route canonica con:

- un URI con il prefisso del locale e, quando disponibile, tradotto tramite i tuoi file di lingua;
- un nome composto dal nome canonico più il locale, come `example.es`;
- il locale dell'applicazione impostato sulla sua lingua per ogni richiesta che serve.

Le route tradotte vengono generate dopo il boot di tutti i service provider, quindi definisci le tue route come sempre nei file di route e vengono rilevate automaticamente. Funzionano anche con la [cache delle route](url-generation#cache-delle-route).

## Definire le route localizzate

Usa il metodo `lang()` per indicare quali locale deve supportare una route:

```php title="routes/web.php"
Route::get('/example', Controller::class)
    ->lang(['es', 'fr', 'it'])
    ->name('example');
```

Supponendo che il fallback locale sia `en` e che in `lang/es/routes.php` esista una traduzione spagnola per `example`, vengono registrate quattro route:

| URI | Nome | Locale |
| --- | --- | --- |
| `/example` | `example` | `en` |
| `/es/ejemplo` | `example.es` | `es` |
| `/fr/example` | `example.fr` | `fr` |
| `/it/example` | `example.it` | `it` |

Le route francese e italiana non hanno una traduzione, quindi mantengono l'URI originale dopo il prefisso del locale. Vedi [Tradurre gli URI delle route](#tradurre-gli-uri-delle-route) per aggiungerne una.

### Il fallback locale

La route canonica serve il `fallback_locale` di Laravel (`APP_FALLBACK_LOCALE` nel `.env`). Deve corrispondere alla lingua in cui sono davvero scritti le tue route e i tuoi contenuti: se i tuoi URI sono in inglese, il fallback locale deve essere `en`.

Elencare il fallback locale in `lang()` non crea problemi: la route canonica lo serve già, quindi non viene registrata nessuna route in più.

```php title="routes/web.php"
// Same result as ->lang(['es']) when the fallback locale is 'en'
Route::get('/example', Controller::class)
    ->lang(['en', 'es']);
```

## Gruppi di route

Per non ripetere le stesse lingue su ogni route, usa `Route::lang()->group()`:

```php title="routes/web.php"
Route::lang(['es', 'it'])->group(function () {
    Route::get('/example', fn () => 'Hello, World!');
    Route::get('/another', fn () => 'Another route');
});
```

Tutte le route del gruppo ne ereditano i locale. Chiama `lang()` su una singola route per aggiungere altre lingue a quelle del gruppo:

```php title="routes/web.php"
Route::lang(['es', 'it'])->group(function () {
    Route::get('/example', fn () => 'Hello, World!'); // es, it
    Route::get('/another', fn () => 'Another route')
        ->lang(['fr']); // es, it, fr
});
```

`lang()` si combina con gli altri attributi dei gruppi, come middleware, prefissi e domini:

```php title="routes/web.php"
Route::middleware('auth')->lang(['es', 'it'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});
```

## Il locale dell'applicazione

Ogni route localizzata imposta il locale dell'applicazione sulla propria lingua prima che venga eseguito il tuo codice:

- una route tradotta usa il suo locale, quindi `/es/ejemplo` viene eseguita con `app()->getLocale()` uguale a `es`;
- la route canonica usa il fallback locale, quindi `/example` viene eseguita con `en`;
- le route senza `lang()` lasciano invariato il locale dell'applicazione, che per impostazione predefinita è `APP_LOCALE`.

Questo significa che `__()`, i messaggi di validazione, le date e tutto ciò che dipende dal locale seguono automaticamente l'URL. Con una traduzione spagnola in `lang/es.json`:

```php title="routes/web.php"
Route::get('/welcome', fn () => __('Welcome!'))
    ->lang(['es']);

// GET /welcome    → "Welcome!"
// GET /es/welcome → "¡Bienvenido!"
```

Il locale è già impostato quando viene eseguito il route model binding, quindi i tuoi binding possono risolvere slug tradotti o altri valori che dipendono dal locale.

> [!WARNING]
> Il locale viene impostato da un middleware che il package aggiunge ai gruppi di middleware `web` e `api`. Le route definite in `routes/web.php` e `routes/api.php` appartengono a questi gruppi per impostazione predefinita. Se registri route localizzate al di fuori di essi, aggiungi tu il middleware `Goodcat\L10n\Middleware\SetLocale`.

Per leggere il locale della route corrente, usa `$request->route()->locale()`. Vedi [Helper delle route](helpers#helper-delle-route).

## Nomi delle route

Le route tradotte prendono il nome della route canonica, seguito da un punto e dal locale:

```php
Route::get('/example', Controller::class)
    ->lang(['es', 'it'])
    ->name('example');

// Registered names: example, example.es, example.it
```

Raramente ti servono questi nomi direttamente. Chiama `route('example')` e il package sceglie la route per il locale corrente, oppure passa `lang` per sceglierne un'altra. Vedi [Generazione degli URL](url-generation).

Per controllare la route corrente, usa `L10n::is('example')` invece di `Route::is('example')`: corrisponde alla route canonica e a tutte le sue traduzioni. Vedi [Corrispondenza delle route](helpers#corrispondenza-delle-route).

Anche le route senza nome vengono localizzate: il package assegna a loro, e alle loro traduzioni, un nome interno che inizia con `generated::`.

## Strategia di routing

L'opzione `route_strategy` in `config/l10n.php` controlla come vengono applicati i prefissi di lingua. Le strategie sono tre:

- `prefix_except_default` (predefinita) lascia senza prefisso il fallback locale e aggiunge il prefisso a tutti gli altri locale.
- `prefix` aggiunge il prefisso a ogni locale, compreso il fallback, e non registra nessuna route senza prefisso.
- `no_prefix` usa gli URI tradotti senza alcun prefisso di lingua.

Ecco come si confrontano per `Route::get('/example')->lang(['es', 'it'])`, con una traduzione spagnola per `example` e nessuna italiana:

| Locale | `prefix_except_default` | `prefix` | `no_prefix` |
| --- | --- | --- | --- |
| `en` (fallback) | `/example` | `/en/example` | `/example` |
| `es` | `/es/ejemplo` | `/es/ejemplo` | `/ejemplo` |
| `it` | `/it/example` | `/it/example` | non registrata |

Con `prefix`, una richiesta a `/example` restituisce 404: il fallback locale si trova su `/en/example` come qualsiasi altra lingua. Con `no_prefix`, la route italiana avrebbe lo stesso URI di quella canonica, quindi viene saltata (vedi *Collisioni con no_prefix* in [Tradurre gli URI delle route](#tradurre-gli-uri-delle-route)).

Per cambiare strategia, pubblica la configurazione del package:

```bash
php artisan vendor:publish --tag=l10n-config
```

Poi imposta l'opzione nel file pubblicato:

```php title="config/l10n.php"
return [
    'route_strategy' => 'prefix',
];
```

La strategia vale per tutte le route localizzate dell'applicazione.

## Tradurre gli URI delle route

Gestisci le traduzioni delle route in un file `routes.php` per ogni lingua, dentro la cartella `lang`:

```txt
/lang
├── /es
│   └── routes.php
├── /fr
│   └── routes.php
└── /it
    └── routes.php
```

In ogni file, associa l'URI originale della route alla sua traduzione:

```php title="lang/es/routes.php"
return [
    'example' => 'ejemplo',
    'products/{product}' => 'productos/{product}',
];
```

Se per un locale non c'è una traduzione, l'URI originale viene usato così com'è.

> [!NOTE]
> La chiave è l'URI della route **senza** lo slash iniziale. Per esempio, per `Route::get('/example')` la chiave è `example`. Gli slash iniziali e finali nel valore tradotto vengono ignorati.

Mantieni nell'URI tradotto i parametri della route con gli stessi nomi, così la route tradotta riceve gli stessi parametri di quella canonica. Vanno tradotti solo i segmenti statici.

### Chiavi di binding personalizzate

Per le route con una chiave di binding personalizzata, ometti la chiave (`:slug`) sia dalla chiave di traduzione sia dall'URI tradotto:

<!-- code-group -->

```php title="routes/web.php"
Route::get('/article/{post:slug}', [PostController::class, 'show'])
    ->lang(['it']);
```

```php title="lang/it/routes.php"
return [
    'article/{post}' => 'articolo/{post}',
];
```

<!-- /code-group -->

La route localizzata continua a risolvere `post` tramite il suo slug.

### Collisioni con no_prefix

Senza un prefisso di lingua, due route possono finire con lo stesso URI. Con la strategia `no_prefix`, una traduzione che condivide dominio e URI con la route canonica o con una traduzione precedente viene saltata:

```php title="routes/web.php"
// lang/es/routes.php has no translation for 'dashboard'
Route::get('/dashboard', DashboardController::class)
    ->lang(['es'])
    ->name('dashboard');
```

Qui `/dashboard` è servita solo dalla route canonica, con il fallback locale. Anche `route('dashboard', ['lang' => 'es'])` restituisce l'URL canonico. Per servire i visitatori spagnoli su un URL dedicato, aggiungi una traduzione per `dashboard` in `lang/es/routes.php`.

## Traduzione dei domini

Se la tua applicazione usa il routing basato sui domini, puoi tradurre i domini negli stessi file di lingua `routes.php`. La chiave è il dominio originale:

```php title="lang/es/routes.php"
return [
    'example' => 'ejemplo',
    'example.com' => 'es.example.com',
];
```

Quando un locale ha un dominio tradotto, le sue route non hanno prefisso: è già il dominio a identificare la lingua. I locale senza un dominio tradotto mantengono il prefisso nel percorso sul dominio originale:

```php title="routes/web.php"
Route::domain('example.com')->lang(['es', 'it'])->group(function () {
    Route::get('/example', Controller::class);
});
```

| Locale | `prefix_except_default` | `prefix` |
| --- | --- | --- |
| `en` (fallback) | `example.com/example` | `example.com/en/example` |
| `es` (dominio tradotto) | `es.example.com/ejemplo` | `es.example.com/ejemplo` |
| `it` | `example.com/it/example` | `example.com/it/example` |

Con la strategia `prefix`, il fallback locale mantiene il prefisso nel percorso sul dominio originale, perché non ha un dominio tradotto.

> [!NOTE]
> Se usi i domini tradotti insieme alla [lingua preferita](locale-preference), configura una sessione condivisa tra quei domini.

## Cosa ereditano le route tradotte

Una route tradotta è una copia della sua route canonica, quindi mantiene tutto quello che hai definito su di essa:

- il controller o la closure;
- i middleware, compresi quelli ereditati dai gruppi;
- i vincoli sui parametri definiti con `where()` e i valori predefiniti definiti con `defaults()`;
- le opzioni del route model binding, come le chiavi personalizzate e `withTrashed()`;
- le route `Route::fallback()`, le cui traduzioni sono anch'esse route di fallback.

Definisci la route una volta sola e ogni lingua si comporta allo stesso modo, cambiano solo URL e locale.
