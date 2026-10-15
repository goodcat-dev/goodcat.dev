---
title: 'Generazione degli URL in JavaScript'
description: 'Genera URL localizzati nel tuo frontend JavaScript o TypeScript con l''helper per Wayfinder o per Ziggy.'
---

Questo package fornisce funzioni helper per generare URL localizzati nel tuo frontend JavaScript/TypeScript. Scegli l'helper per [Wayfinder](https://github.com/laravel/wayfinder) o per [Ziggy](https://github.com/tightenco/ziggy).

Gli esempi di questa pagina usano questa definizione di route:

```php title="routes/web.php"
Route::get('/foo/{id}', Controller::class)
    ->lang(['it', 'es'])
    ->name('foo');
```

## Wayfinder

Se usi Wayfinder, pubblica l'helper TypeScript:

```bash
php artisan vendor:publish --tag=l10n-wayfinder
```

Viene creato un file `resources/js/l10n.ts` che esporta un helper `route(routes, params?, options?)`, il quale sceglie la route localizzata adatta in base al locale corrente.

> [!NOTE]
> L'helper `route()` funziona solo con le route con nome, le action non sono supportate.

Importa l'helper `route` e passagli le funzioni di route generate da Wayfinder:

```ts
import { route } from '@/l10n';
import foo from '@/routes/foo';

const esUrl = route(foo, { id: 1, lang: 'es' }).url;
```

Viene esportato anche il tipo `LocalizedRoutes`, per tipizzare i tuoi wrapper e composable.

Il locale viene determinato in quest'ordine:

1. Il parametro `lang`, se presente.
2. L'attributo `lang` dell'elemento `<html lang="en">`, normalizzato nel formato di locale di Laravel (`pt-BR` corrisponde a una route `pt_BR`).
3. La route canonica, quando nessuna route localizzata corrisponde.

Durante il rendering lato server, passa `lang` esplicitamente per scegliere una route localizzata; senza, l'helper usa la route canonica.

Per chiamare direttamente la route canonica, usa `foo.__canonical({ id: 1 })`.

## Ziggy

Se usi Ziggy, pubblica l'helper JavaScript:

```bash
php artisan vendor:publish --tag=l10n-ziggy
```

Viene creato un file `resources/js/l10n.js` con una funzione `route`.

Usa `route()` al posto della funzione `route()` di Ziggy, senza altre modifiche:

```js
import { route } from '@/l10n';

route('foo', { id: 1, lang: 'it' });
```

L'helper usa prima il parametro `lang`, poi l'attributo HTML `lang`, e ripiega sulla route canonica se nessuna traduzione corrisponde. Anche i locale regionali come `pt-BR` corrispondono al formato di Laravel con l'underscore, `pt_BR`.

Durante il rendering lato server, passa `lang` esplicitamente per scegliere una route localizzata; senza, l'helper usa la route canonica.

Tutti gli argomenti di Ziggy vengono inoltrati, compreso un oggetto di configurazione esplicito come quarto argomento. Chiamare `route()` senza nome di route restituisce come sempre l'istanza `Router` di Ziggy.
