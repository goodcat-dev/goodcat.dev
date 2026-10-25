---
title: 'Filter Objects'
description: 'Keep the filters of a model in a dedicated class by overriding getQueryStringObject().'
---

## Moving filters out of the model

By default, the package looks for filters on the model itself. When a model has many filters, or when you want to keep them apart from the rest of the model, move them to a dedicated class and return it from `getQueryStringObject()`:

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

The filter object defines public methods with the same attribute and the same arguments as model filters:

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

The class needs no base class, interface or trait. Nothing else changes: you still call `Product::query()->queryString($request)`.

## Where filters are read from

The filters are read only from the object returned by `getQueryStringObject()`. Once you override it, methods tagged on the model are no longer called: keep all the filters of a model in one place.

`getQueryStringObject()` must stay **public**: the [cache command](caching) calls it from outside the model.

## Injecting dependencies

The object is created every time the scope runs. To inject services into its constructor, resolve it from the container:

```php title="app/Models/Product.php"
public function getQueryStringObject(): object
{
    return app(ProductFilters::class);
}
```

## Sharing filters between models

Since a filter object is a regular class, several models can return the same one, for example to share filters on columns they have in common. Each model is filtered independently, with its own query builder.
