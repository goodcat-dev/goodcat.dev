---
title: 'Applying Filters'
description: 'Run the filters of a model with the queryString() scope, from the current request or from an array of values.'
---

## The queryString() scope

The `UseQueryString` trait adds the `queryString()` scope to the model. Call it with the current request to run the filters whose parameters are in it:

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

From a request, the scope reads the **URL query parameters**. The request body is ignored, including for `POST` requests.

Parameters without a matching filter are ignored, so pagination, sorting and any other parameter can share the query string with your filters.

## Combining with other constraints

`queryString()` is a regular local scope: chain it with any other constraint, before or after it.

```php
$products = Product::query()
    ->where('active', true)
    ->queryString($request)
    ->orderBy('name')
    ->get();
```

It works on relationships too, as long as the related model uses the trait:

```php
$products = $category->products()->queryString($request)->get();
```

The filters only add constraints to the query you pass them. Keep the constraints that must always apply, such as authorization rules, outside the filters.

## Passing an array

The scope also accepts an array of values. Use it to filter with data you prepared, for example after validation:

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

The keys are matched against the filter names, exactly as request parameters are. Values are passed to the filters as they are: arrays don't go through the request middleware, so their strings aren't trimmed and empty strings aren't converted to `null`.

`null` values in the array are skipped like request ones, unless [`allows_null`](filter-methods#null-values) is enabled.
