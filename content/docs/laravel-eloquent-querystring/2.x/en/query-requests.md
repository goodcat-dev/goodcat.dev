---
title: 'QUERY Requests'
description: 'Read filters from the JSON or form body of requests sent with the HTTP QUERY method.'
---

## The QUERY method

`QUERY` is an HTTP method for safe, idempotent requests that carry their parameters in the body, like a `GET` with a body. It's useful when filters are too long or too structured for a URL.

Filtering from `QUERY` requests requires version 2.2 or higher.

## Defining the route

Register the route for the `QUERY` method and apply the filters as usual:

```php title="routes/api.php"
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['QUERY'], '/users', function (Request $request) {
    return User::query()->queryString($request)->get();
});
```

The filter methods don't change: the same filter serves `GET` and `QUERY` requests.

## Sending the filters

The body can be JSON:

```bash
curl -X QUERY https://example.com/api/users \
    -H 'Content-Type: application/json' \
    -d '{"email":"john@doe.com"}'
```

Or a URL-encoded form:

```bash
curl -X QUERY https://example.com/api/users \
    --data-urlencode 'email=john@doe.com'
```

## Where filters are read from

For `QUERY` requests, filters come **only from the body**. URL parameters are ignored, even when the body is empty. For every other method, including `POST`, filters come only from the URL.

A JSON body can carry other types than strings, such as numbers, booleans and nested objects. Type your filter arguments accordingly, or [validate the body](applying-filters#passing-an-array) and pass the validated data to the scope.
