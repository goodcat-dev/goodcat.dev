---
title: 'Componenti Blade'
description: 'Componenti Blade per i link alternate hreflang e per un selettore di lingua, con template personalizzabili.'
---

## Link alternate hreflang

Il package fornisce un componente Blade che genera i tag `<link rel="alternate" hreflang="...">` seguendo le [linee guida di Google per le versioni localizzate](https://developers.google.com/search/docs/specialty/international/localized-versions).

Aggiungi il componente nell'`<head>` del tuo layout:

```blade
<head>
    <x-l10n::alternate />
</head>
```

Per una route con le traduzioni `es` e `it`, viene generato:

```blade
<link rel="alternate" hreflang="en" href="https://example.com/products/42" />
<link rel="alternate" hreflang="es" href="https://example.com/es/productos/42" />
<link rel="alternate" hreflang="it" href="https://example.com/it/products/42" />
<link rel="alternate" hreflang="x-default" href="https://example.com/products/42" />
```

Il componente include tutte le varianti localizzate, il fallback locale e una voce `x-default` che punta alla route canonica. Ogni link mantiene la query string della richiesta corrente. Non genera nulla per le route senza traduzioni e salta i locale senza una [chiave di route localizzata](localized-routes#chiavi-di-route-tradotte).

## Selettore di lingua

Il package fornisce un componente Blade che mostra un elemento `<select>` per passare da un locale disponibile all'altro. Quando l'utente sceglie un locale diverso, la pagina viene ricaricata sull'URL localizzato corrispondente.

```blade
<x-l10n::switcher />
```

Per una route con le traduzioni `es` e `it`, viene generato:

```blade
<select onchange="window.location = this.value">
    <option value="https://example.com/products/42" selected>en</option>
    <option value="https://example.com/es/productos/42">es</option>
    <option value="https://example.com/it/products/42">it</option>
</select>
```

Il locale corrente viene selezionato automaticamente e la query string della richiesta corrente viene mantenuta. Il componente non genera nulla per le route senza traduzioni e salta i locale senza una [chiave di route localizzata](localized-routes#chiavi-di-route-tradotte).

Puoi passare al componente qualsiasi attributo HTML:

```blade
<x-l10n::switcher class="locale-select" id="locale" />
```

## Personalizzare i template

Per personalizzare l'HTML generato dai componenti Blade, pubblica le view:

```bash
php artisan vendor:publish --tag=l10n-views
```

Modifica i template pubblicati in `resources/views/vendor/l10n/components/`. Ogni template elenca le variabili disponibili per la personalizzazione.
