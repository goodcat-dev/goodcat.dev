---
title: 'Laravel Eloquent Query String'
description: 'Filtra i model Eloquent dai parametri della query string. Marca un metodo del model con un attributo PHP e verrà eseguito ogni volta che il suo parametro è nella richiesta.'
guides:
  - page: filter-methods
    description: 'Associa i parametri della query string ai metodi del model con l’attributo QueryString.'
  - page: applying-filters
    description: 'Applica a qualsiasi query Eloquent i filtri di una richiesta, o di un array che hai preparato.'
features:
  - page: query-requests
    description: 'Leggi i filtri dal body JSON o form delle richieste HTTP QUERY.'
  - page: filter-objects
    description: 'Sposta i filtri fuori dal model, in una classe dedicata.'
  - page: caching
    description: 'Evita la scoperta degli attributi in produzione con un solo comando Artisan.'
---

## In breve

Aggiungi il trait `UseQueryString` a un model e marca un metodo pubblico con l'attributo `QueryString`. L'attributo indica il parametro della query string a cui il metodo risponde:

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

Poi applica i filtri della richiesta corrente con lo scope `queryString()`:

```php title="routes/web.php"
Route::get('/posts', function (Request $request) {
    return Post::query()->queryString($request)->paginate();
});
```

Ogni richiesta esegue solo i filtri che chiede:

- `/posts` restituisce tutti i post;
- `/posts?author=matteo` restituisce i post scritti da `matteo`;
- `/posts?author=matteo&published=yes` restituisce solo quelli pubblicati.

I parametri senza un filtro corrispondente, come `page`, vengono ignorati.

## Costruito su Eloquent

`laravel-eloquent-querystring` fa una cosa sola: chiama i tuoi metodi. Tutto il resto è l'Eloquent che conosci già:

- un **filtro** è un normale metodo che riceve il query builder, quindi può usare `where()`, le relazioni, altri scope e tutto ciò che il builder supporta;
- lo **scope `queryString()`** funziona ovunque funzionino gli scope locali: su `Model::query()`, sulle relazioni e insieme agli altri vincoli della query;
- i **valori della richiesta** passano prima dai middleware di Laravel, quindi le stringhe vuote sono già convertite in `null` e vengono saltate;
- i **filtri restano accanto alla logica di query** del model, oppure in un [oggetto filtro](filter-objects) se preferisci una classe separata.

Non c'è nessuna configurazione da scrivere prima di iniziare: installa il package, marca un metodo, chiama lo scope.
