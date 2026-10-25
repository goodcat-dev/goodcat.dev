---
title: 'Blade Components'
description: 'Blade components for alternate hreflang links and a locale switcher, with customizable templates.'
---

## Alternate hreflang links

The package provides a Blade component that generates `<link rel="alternate" hreflang="...">` tags following [Google's guidelines for localized versions](https://developers.google.com/search/docs/specialty/international/localized-versions).

Add the component to the `<head>` of your layout:

```blade
<head>
    <x-l10n::alternate />
</head>
```

For a route with `es` and `it` translations, this will render:

```blade
<link rel="alternate" hreflang="en" href="https://example.com/products/42" />
<link rel="alternate" hreflang="es" href="https://example.com/es/productos/42" />
<link rel="alternate" hreflang="it" href="https://example.com/it/products/42" />
<link rel="alternate" hreflang="x-default" href="https://example.com/products/42" />
```

The component includes all localized variants, the fallback locale, and an `x-default` entry pointing to the canonical route. Every link keeps the query string of the current request. It renders nothing for routes without translations, and skips locales without a [localized route key](localized-routes#translated-route-keys).

## Locale switcher

The package provides a Blade component that renders a `<select>` element for switching between available locales. When the user selects a different locale, the page reloads to the corresponding localized URL.

```blade
<x-l10n::switcher />
```

For a route with `es` and `it` translations, this will render:

```blade
<select onchange="window.location = this.value">
    <option value="https://example.com/products/42" selected>en</option>
    <option value="https://example.com/es/productos/42">es</option>
    <option value="https://example.com/it/products/42">it</option>
</select>
```

The current locale is automatically selected, and the query string of the current request is kept. The component renders nothing for routes without translations, and skips locales without a [localized route key](localized-routes#translated-route-keys).

You can pass any HTML attribute to the component:

```blade
<x-l10n::switcher class="locale-select" id="locale" />
```

## Customizing the templates

To customize the HTML output of the Blade components, publish the views:

```bash
php artisan vendor:publish --tag=l10n-views
```

Edit the published templates in `resources/views/vendor/l10n/components/`. Each template lists the variables available for customization.
