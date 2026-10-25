---
title: 'Oggetti filtro'
description: 'Tieni i filtri di un model in una classe dedicata sovrascrivendo getQueryStringObject().'
---

## Spostare i filtri fuori dal model

Di default il package cerca i filtri nel model stesso. Quando un model ha molti filtri, o quando vuoi tenerli separati dal resto del model, spostali in una classe dedicata e restituiscila da `getQueryStringObject()`:

```php title="app/Models/Product.php"
use App\Filters\ProductFilters;
use Goodcat\QueryString\Traits\UseQueryString;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use UseQueryString;

    public function getQueryStringObject(): object
    {
        return new ProductFilters;
    }
}
```

L'oggetto filtro definisce metodi pubblici con lo stesso attributo e gli stessi argomenti dei filtri del model:

```php title="app/Filters/ProductFilters.php"
namespace App\Filters;

use Goodcat\QueryString\Attributes\QueryString;
use Illuminate\Database\Eloquent\Builder;

class ProductFilters
{
    #[QueryString('category')]
    public function filterByCategory(Builder $query, string $category): void
    {
        $query->where('category', $category);
    }

    #[QueryString('min_price')]
    public function filterByMinPrice(Builder $query, string $price): void
    {
        $query->where('price', '>=', $price);
    }
}
```

La classe non richiede classi base, interfacce o trait. Per il resto non cambia nulla: continui a chiamare `Product::query()->queryString($request)`.

## Da dove vengono letti i filtri

I filtri vengono letti solo dall'oggetto restituito da `getQueryStringObject()`. Una volta sovrascritto il metodo, i metodi marcati nel model non vengono più chiamati: tieni tutti i filtri di un model in un unico posto.

`getQueryStringObject()` deve restare **pubblico**: il [comando di cache](caching) lo chiama dall'esterno del model.

## Iniettare dipendenze

L'oggetto viene creato ogni volta che lo scope viene eseguito. Per iniettare servizi nel suo costruttore, risolvilo dal container:

```php title="app/Models/Product.php"
public function getQueryStringObject(): object
{
    return app(ProductFilters::class);
}
```

## Condividere i filtri tra model

Un oggetto filtro è una normale classe, quindi più model possono restituire lo stesso, per esempio per condividere i filtri sulle colonne che hanno in comune. Ogni model viene filtrato in modo indipendente, con il proprio query builder.
