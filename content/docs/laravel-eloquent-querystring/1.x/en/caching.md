---
title: 'Caching'
description: 'Cache the discovered filters in production and know when the cache needs to be regenerated.'
---

## Why cache

To find your filters, the package reads the attributes of the model's methods every time the `queryString()` scope runs. The cost is small, but in production you can skip it by caching the filters of all your models:

```bash
php artisan querystring:cache
```

The command writes the map of every model's filters to `bootstrap/cache/querystring.php`. To remove it:

```bash
php artisan querystring:clear
```

## Laravel's optimize command

The cache is part of Laravel's `optimize` command, so if your deployment already runs it there is nothing to add:

```bash
php artisan optimize        # also runs querystring:cache
php artisan optimize:clear  # also runs querystring:clear
```

## Regenerating the cache

While the cache exists, the package doesn't read the attributes anymore: it uses the cached map. Regenerate it whenever you add, rename or remove a filter, or add the trait to a new model. Otherwise the change has no effect.

In local development you usually don't need the cache at all. If you run `optimize` locally, remember to clear it before working on filters.

The command removes the previous cache before generating the new one. If generating fails, for example because a model throws an exception in its constructor, no cache is left and filters are read from the attributes again.

## Which models are cached

The command looks for models under your application's `app` directory. A model is included when it:

- extends Eloquent's `Model`;
- uses the `UseQueryString` trait directly in its own class.

Models that inherit the trait from a parent class or receive it through another trait are not found. Use the trait on each concrete model. An abstract model that uses the trait makes the command fail, because it can't be instantiated.

To cache it, the command creates each model with `new` and calls its `getQueryStringObject()` method, so the cached filters are those of the object returned at that moment.

> [!WARNING]
> While the cache exists, a model that is not in it is filtered with no filters at all: its `queryString()` scope does nothing. This happens to models outside the scanned directory, for example in a package or in a module with its own namespace.

To scan a different directory, pass its namespace and path as declared in the `autoload.psr-4` section of `composer.json`:

```bash
php artisan querystring:cache --namespace="Domain\\" --path=src/Domain
```

Each run replaces the whole cache with the models of the given directory.
