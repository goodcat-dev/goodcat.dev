---
title: 'Applicare i filtri'
description: 'Esegui i filtri di un model con lo scope queryString(), a partire dalla richiesta corrente o da un array di valori.'
---

## Lo scope queryString()

Il trait `UseQueryString` aggiunge lo scope `queryString()` al model. Chiamalo con la richiesta corrente per eseguire i filtri i cui parametri sono presenti:

```php title="app/Http/Controllers/ProductController.php"
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->queryString($request)
            ->latest()
            ->paginate();

        return view('products.index', ['products' => $products]);
    }
}
```

Da una richiesta, lo scope legge i **parametri della query string dell'URL**. Il body della richiesta viene ignorato, anche per le richieste `POST`. L'unica eccezione è il [metodo HTTP QUERY](query-requests), i cui filtri arrivano dal body.

I parametri senza un filtro corrispondente vengono ignorati, quindi paginazione, ordinamento e qualsiasi altro parametro possono condividere la query string con i tuoi filtri.

## Combinarlo con altri vincoli

`queryString()` è un normale scope locale: concatenalo con qualsiasi altro vincolo, prima o dopo.

```php
$products = Product::query()
    ->where('active', true)
    ->queryString($request)
    ->orderBy('name')
    ->get();
```

Funziona anche sulle relazioni, purché il model correlato usi il trait:

```php
$products = $category->products()->queryString($request)->get();
```

I filtri aggiungono solo vincoli alla query che ricevono. Tieni fuori dai filtri i vincoli che devono valere sempre, come le regole di autorizzazione.

## Passare un array

Lo scope accetta anche un array di valori. Usalo per filtrare con dati che hai preparato, per esempio dopo la validazione:

```php title="app/Http/Controllers/ProductController.php"
public function index(Request $request)
{
    $filters = $request->validate([
        'category' => ['nullable', 'string', 'max:50'],
        'tags' => ['nullable', 'array'],
        'tags.*' => ['string'],
    ]);

    return Product::query()->queryString($filters)->paginate();
}
```

Le chiavi vengono confrontate con i nomi dei filtri, esattamente come i parametri della richiesta. I valori vengono passati ai filtri così come sono: un array non passa dai middleware della richiesta, quindi le sue stringhe non vengono ripulite dagli spazi e quelle vuote non diventano `null`.

I valori `null` dell'array vengono saltati come quelli della richiesta, a meno che [`allows_null`](filter-methods#valori-null) non sia attivo.
