---
title: 'Upgrade Guide'
description: 'Upgrade from version 1.x to 2.x: the package name changes, the API stays the same.'
---

## Upgrading from 1.x

Version 2.0 contains no breaking changes to the API: filters, the `queryString()` scope, filter objects and the cache work as before. The major version marks the new package name and the support for Laravel 13.

### Package name

The package is now published as `goodcat/laravel-eloquent-querystring`. Replace the old name in your `composer.json`, whether it is `goodcat/laravel-querystring` or `goodcat/laravel-filter-querystring`:

```json title="composer.json"
"require": {
    "goodcat/laravel-eloquent-querystring": "^2.2"
}
```

Then update your dependencies:

```bash
composer update goodcat/laravel-eloquent-querystring
```

The PHP namespace is still `Goodcat\QueryString`, so your code needs no changes.

### Requirements

Version 2.x supports Laravel 11, 12 and 13 and requires PHP 8.2 or higher, like 1.x.

### Cache

Regenerate the filter cache after upgrading:

```bash
php artisan querystring:cache
```

## New in 2.x

- [Several names in one attribute](filter-methods#parameter-names), such as `#[QueryString('name', 'email')]`, since 2.1.
- [Filters from the body of HTTP QUERY requests](query-requests), since 2.2.
- [Cached models](caching#which-models-are-cached) include those that inherit the trait from a parent class or another trait, since 2.2.1.
