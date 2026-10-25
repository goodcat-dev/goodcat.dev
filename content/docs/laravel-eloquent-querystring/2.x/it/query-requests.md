---
title: 'Richieste QUERY'
description: 'Leggi i filtri dal body JSON o form delle richieste inviate con il metodo HTTP QUERY.'
---

## Il metodo QUERY

`QUERY` è un metodo HTTP per richieste sicure e idempotenti che portano i parametri nel body, come una `GET` con un body. È utile quando i filtri sono troppo lunghi o troppo strutturati per un URL.

Filtrare dalle richieste `QUERY` richiede la versione 2.2 o superiore.

## Definire la route

Registra la route per il metodo `QUERY` e applica i filtri come al solito:

```php title="routes/api.php"
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['QUERY'], '/users', function (Request $request) {
    return User::query()->queryString($request)->get();
});
```

I metodi filtro non cambiano: lo stesso filtro serve le richieste `GET` e `QUERY`.

## Inviare i filtri

Il body può essere JSON:

```bash
curl -X QUERY https://example.com/api/users \
    -H 'Content-Type: application/json' \
    -d '{"email":"john@doe.com"}'
```

Oppure un form URL-encoded:

```bash
curl -X QUERY https://example.com/api/users \
    --data-urlencode 'email=john@doe.com'
```

## Da dove vengono letti i filtri

Per le richieste `QUERY` i filtri arrivano **solo dal body**. I parametri dell'URL vengono ignorati, anche quando il body è vuoto. Per tutti gli altri metodi, compreso `POST`, i filtri arrivano solo dall'URL.

Un body JSON può contenere tipi diversi dalle stringhe, come numeri, booleani e oggetti annidati. Tipizza di conseguenza gli argomenti dei filtri, oppure [valida il body](applying-filters#passare-un-array) e passa allo scope i dati validati.
