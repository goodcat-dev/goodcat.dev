---
title: 'Cache'
description: 'Metti in cache i filtri trovati in produzione e scopri quando la cache va rigenerata.'
---

## Perché usare la cache

Per trovare i filtri, il package legge gli attributi dei metodi del model ogni volta che lo scope `queryString()` viene eseguito. Il costo è basso, ma in produzione puoi evitarlo mettendo in cache i filtri di tutti i model:

```bash
php artisan querystring:cache
```

Il comando scrive la mappa dei filtri di ogni model in `bootstrap/cache/querystring.php`. Per rimuoverla:

```bash
php artisan querystring:clear
```

## Il comando optimize di Laravel

La cache fa parte del comando `optimize` di Laravel, quindi se il tuo deploy lo esegue già non c'è nulla da aggiungere:

```bash
php artisan optimize        # esegue anche querystring:cache
php artisan optimize:clear  # esegue anche querystring:clear
```

## Rigenerare la cache

Finché la cache esiste, il package non legge più gli attributi: usa la mappa salvata. Rigenerala ogni volta che aggiungi, rinomini o rimuovi un filtro, o aggiungi il trait a un nuovo model. Altrimenti la modifica non ha effetto.

In sviluppo di solito la cache non serve. Se esegui `optimize` in locale, ricordati di svuotarla prima di lavorare sui filtri.

Se la generazione della cache fallisce, per esempio perché un model lancia un'eccezione nel costruttore, la cache precedente viene mantenuta.

## Quali model finiscono in cache

Il comando cerca i model nella directory `app` dell'applicazione. Un model viene incluso quando:

- estende il `Model` di Eloquent;
- può essere istanziato, quindi i model astratti vengono saltati;
- usa il trait `UseQueryString`, direttamente, tramite una classe padre o tramite un altro trait.

Per metterlo in cache, il comando crea ogni model con `new` e chiama il suo metodo `getQueryStringObject()`, quindi in cache finiscono i filtri dell'oggetto restituito in quel momento.

> [!WARNING]
> Finché la cache esiste, un model che non è presente al suo interno viene filtrato senza alcun filtro: il suo scope `queryString()` non fa nulla. Succede ai model fuori dalla directory analizzata, per esempio in un package o in un modulo con un proprio namespace.

Per analizzare una directory diversa, passa il suo namespace e il suo percorso come sono dichiarati nella sezione `autoload.psr-4` di `composer.json`:

```bash
php artisan querystring:cache --namespace="Domain\\" --path=src/Domain
```

Ogni esecuzione sostituisce l'intera cache con i model della directory indicata.
