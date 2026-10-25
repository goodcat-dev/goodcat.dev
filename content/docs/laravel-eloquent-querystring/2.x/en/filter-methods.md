---
title: 'Filter Methods'
description: 'Tag model methods with the QueryString attribute, map one or more parameters to them and handle the values they receive.'
---

## Defining a filter

A filter is a **public** method of the model, tagged with the `QueryString` attribute:

```php title="app/Models/Product.php"
use Goodcat\QueryString\Attributes\QueryString;
use Goodcat\QueryString\Traits\UseQueryString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use UseQueryString;

    #[QueryString('category')]
    public function filterByCategory(Builder $query, string $category): void
    {
        $query->where('category', $category);
    }
}
```

When the `category` parameter is in the request, the package calls `filterByCategory()` with the query and the parameter's value. The method changes the query in place: its return value is ignored.

Protected and private methods are never called, even when they carry the attribute.

## Method arguments

Every filter is called with three arguments:

1. the Eloquent **query builder** being filtered;
2. the parameter's **value**;
3. the parameter's **name**.

Declare only the ones you need. The name is useful when a filter serves more than one parameter:

```php
#[QueryString('name', 'email')]
public function filterByText(Builder $query, string $value, string $name): void
{
    $query->where($name, 'like', $value.'%');
}
```

## Parameter names

Pass more names to the attribute to map several parameters to the same method:

```php
#[QueryString('name', 'email')]
public function filterByText(Builder $query, string $value, string $name): void
```

Repeating the attribute has the same effect:

```php
#[QueryString('name')]
#[QueryString('email')]
public function filterByText(Builder $query, string $value, string $name): void
```

Passing several names to one attribute requires version 2.1 or higher.

Names are matched exactly and are case-sensitive: `#[QueryString('email')]` doesn't respond to `?Email=`. Each name belongs to a single method: if two methods declare the same name, only one of them is called, so keep names unique within a model.

When a request contains more than one filter parameter, the filters run in the order the parameters appear in the request. A filter mapped to two names runs once for each name in the request.

## Parameter values

Values arrive as the request provides them. From a URL, a value is a string, or an array when the parameter uses brackets:

```txt
/products?category=books          → 'books'
/products?tags[]=new&tags[]=sale  → ['new', 'sale']
```

Type the value accordingly:

```php
#[QueryString('tags')]
public function filterByTags(Builder $query, array $tags): void
{
    $query->whereHas('tags', fn (Builder $query) => $query->whereIn('slug', $tags));
}
```

The package doesn't validate or cast values. A request such as `/products?category[]=books` passes an array to a filter that expects a string, and PHP throws a `TypeError`. When the input isn't under your control, validate it first and [pass the validated data](applying-filters#passing-an-array) to the scope.

## Null values

Laravel's default middleware trims request strings and converts empty ones to `null`. `null` values are skipped, so `/products?category=` doesn't run the `category` filter.

To receive `null` values in your filters, publish the configuration file:

```bash
php artisan vendor:publish --provider="Goodcat\QueryString\QueryStringServiceProvider"
```

Then enable `allows_null`:

```php title="config/querystring.php"
return [
    'allows_null' => true,
];
```

With this option, filters run for empty parameters too, so declare a nullable value:

```php
#[QueryString('category')]
public function filterByCategory(Builder $query, ?string $category): void
{
    $category === null
        ? $query->whereNull('category')
        : $query->where('category', $category);
}
```

The option applies to every filter of the application.
