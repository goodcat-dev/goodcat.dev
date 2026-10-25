---
title: 'Laravel Eloquent Query String'
description: 'Filter Eloquent models from URL query parameters. Tag a model method with a PHP attribute and it runs whenever its parameter is in the request.'
guides:
  - page: filter-methods
    description: 'Map query parameters to model methods with the QueryString attribute.'
  - page: applying-filters
    description: 'Apply the filters of a request, or of an array you prepared, to any Eloquent query.'
features:
  - page: filter-objects
    description: 'Move the filters out of the model into a dedicated class.'
  - page: caching
    description: 'Skip the attribute discovery in production with a single Artisan command.'
---

## At a glance

Add the `UseQueryString` trait to a model and tag a public method with the `QueryString` attribute. The attribute names the query parameter the method responds to:

```php title="app/Models/Post.php"
use Goodcat\QueryString\Attributes\QueryString;
use Goodcat\QueryString\Traits\UseQueryString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use UseQueryString;

    #[QueryString('author')]
    public function filterByAuthor(Builder $query, string $author): void
    {
        $query->whereRelation('author', 'username', $author);
    }

    #[QueryString('published')]
    public function filterByPublished(Builder $query, string $published): void
    {
        if ($published === 'yes') {
            $query->whereNotNull('published_at');
        }
    }
}
```

Then apply the filters of the current request with the `queryString()` scope:

```php title="routes/web.php"
Route::get('/posts', function (Request $request) {
    return Post::query()->queryString($request)->paginate();
});
```

Each request runs only the filters it asks for:

- `/posts` returns every post;
- `/posts?author=matteo` returns the posts written by `matteo`;
- `/posts?author=matteo&published=yes` returns only the published ones.

Parameters without a matching filter, such as `page`, are ignored.

## Built on Eloquent

`laravel-eloquent-querystring` does one thing: it calls your methods. Everything else is the Eloquent you already know:

- a **filter** is a regular method that receives the query builder, so it can use `where()`, relationships, other scopes and anything the builder supports;
- the **`queryString()` scope** works wherever local scopes work: on `Model::query()`, on relationships and next to your other query constraints;
- **request values** go through Laravel's middleware first, so empty strings are already converted to `null` and skipped;
- **filters stay next to the query logic** of the model, or in a [filter object](filter-objects) when you prefer a separate class.

There is no configuration to write before you start: install the package, tag a method, call the scope.
