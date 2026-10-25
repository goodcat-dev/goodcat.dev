---
title: 'Quickstart'
description: 'Install laravel-eloquent-querystring, write your first filter and apply it to a query.'
---

## Requirements

- PHP 8.2 or higher
- Laravel 11, 12 or 13

## Installation

Install the package via Composer:

```bash
composer require goodcat/laravel-eloquent-querystring
```

The package registers itself through Laravel's package discovery: there is no service provider to add and, with the defaults, nothing to configure.

## Add the trait

Use the `UseQueryString` trait on the model you want to filter:

```php title="app/Models/User.php"
use Goodcat\QueryString\Traits\UseQueryString;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use UseQueryString;
}
```

The trait adds the `queryString()` scope to the model.

## Write a filter

A filter is a public method tagged with the `QueryString` attribute. The attribute's argument is the name of the query parameter, and the method receives the query builder and the parameter's value:

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

The method name is up to you: only the attribute matters.

## Apply the filters

Call the `queryString()` scope with the current request:

```php title="routes/api.php"
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/users', function (Request $request) {
    return User::query()->queryString($request)->get();
});
```

Request `/api/users?email=john%40doe.com` and the query only returns the users with that email. Without the parameter, the filter doesn't run and every user is returned.

## Prepare for production

The package finds your filters by reading the attributes of your models. In production, cache them with the rest of your application:

```bash
php artisan optimize
```

Remember to run it again on every deployment. See [Caching](caching) for the details.

## What's next?

You now have a model that filters itself from the request. From here, you can:

- [Map more parameters to a filter](filter-methods#parameter-names) and read the parameter's name
- [Filter with validated data](applying-filters#passing-an-array) instead of the raw request
- [Move the filters into a separate class](filter-objects)
- [Accept filters in the body of QUERY requests](query-requests)
