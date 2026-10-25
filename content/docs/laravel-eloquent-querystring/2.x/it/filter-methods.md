---
title: 'Metodi filtro'
description: 'Marca i metodi del model con l’attributo QueryString, associa loro uno o più parametri e gestisci i valori che ricevono.'
---

## Definire un filtro

Un filtro è un metodo **pubblico** del model, marcato con l'attributo `QueryString`:

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

Quando il parametro `category` è nella richiesta, il package chiama `filterByCategory()` con la query e il valore del parametro. Il metodo modifica la query direttamente: il suo valore di ritorno viene ignorato.

I metodi protected e private non vengono mai chiamati, anche se hanno l'attributo.

## Argomenti del metodo

Ogni filtro viene chiamato con tre argomenti:

1. il **query builder** Eloquent da filtrare;
2. il **valore** del parametro;
3. il **nome** del parametro.

Dichiara solo quelli che ti servono. Il nome è utile quando un filtro serve più parametri:

```php
#[QueryString('name', 'email')]
public function filterByText(Builder $query, string $value, string $name): void
{
    $query->where($name, 'like', $value.'%');
}
```

## Nomi dei parametri

Passa più nomi all'attributo per associare diversi parametri allo stesso metodo:

```php
#[QueryString('name', 'email')]
public function filterByText(Builder $query, string $value, string $name): void
```

Ripetere l'attributo ha lo stesso effetto:

```php
#[QueryString('name')]
#[QueryString('email')]
public function filterByText(Builder $query, string $value, string $name): void
```

Passare più nomi a un solo attributo richiede la versione 2.1 o superiore.

I nomi vengono confrontati in modo esatto e distinguono maiuscole e minuscole: `#[QueryString('email')]` non risponde a `?Email=`. Ogni nome appartiene a un solo metodo: se due metodi dichiarano lo stesso nome, ne viene chiamato solo uno, quindi tieni i nomi unici all'interno di un model.

Quando una richiesta contiene più parametri filtro, i filtri vengono eseguiti nell'ordine in cui i parametri compaiono nella richiesta. Un filtro associato a due nomi viene eseguito una volta per ciascun nome presente nella richiesta.

## Valori dei parametri

I valori arrivano così come li fornisce la richiesta. Da un URL, un valore è una stringa, oppure un array quando il parametro usa le parentesi quadre:

```txt
/products?category=books          → 'books'
/products?tags[]=new&tags[]=sale  → ['new', 'sale']
```

Tipizza il valore di conseguenza:

```php
#[QueryString('tags')]
public function filterByTags(Builder $query, array $tags): void
{
    $query->whereHas('tags', fn (Builder $query) => $query->whereIn('slug', $tags));
}
```

Il package non valida né converte i valori. Una richiesta come `/products?category[]=books` passa un array a un filtro che si aspetta una stringa, e PHP lancia un `TypeError`. Quando l'input non è sotto il tuo controllo, validalo prima e [passa i dati validati](applying-filters#passare-un-array) allo scope.

## Valori null

I middleware predefiniti di Laravel tolgono gli spazi dalle stringhe della richiesta e convertono quelle vuote in `null`. I valori `null` vengono saltati, quindi `/products?category=` non esegue il filtro `category`.

Per ricevere i valori `null` nei filtri, pubblica il file di configurazione:

```bash
php artisan vendor:publish --provider="Goodcat\QueryString\QueryStringServiceProvider"
```

Poi attiva `allows_null`:

```php title="config/querystring.php"
return [
    'allows_null' => true,
];
```

Con questa opzione i filtri vengono eseguiti anche per i parametri vuoti, quindi dichiara un valore nullable:

```php
#[QueryString('category')]
public function filterByCategory(Builder $query, ?string $category): void
{
    $category === null
        ? $query->whereNull('category')
        : $query->where('category', $category);
}
```

L'opzione vale per tutti i filtri dell'applicazione.
