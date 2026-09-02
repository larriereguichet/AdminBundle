# Filtering, sorting and pagination

All three apply to collection operations, and all three are implemented as provider decorators
over a Doctrine query builder. A provider that returns an array opts out of all of them.

## Filtering

Declare filters on the operation:

```php
use LAG\AdminBundle\Metadata\Attribute\EntityFilter;
use LAG\AdminBundle\Metadata\Attribute\TextFilter;

new Index(
    grid: 'admin_articles',
    filters: [
        new TextFilter(name: 'search', properties: ['title', 'content']),
        new EntityFilter(
            name: 'author',
            formOptions: ['class' => Author::class, 'choice_label' => 'name'],
        ),
    ],
)
```

From that, the bundle builds a `GET` filter form (`FilterType`) with one field per filter, and
`FilterProvider` applies each submitted value to the query builder through a matching
*applicator*.

### Filter types

**`Filter`** — the base class. Compares one property with one value.

| Option | Default | Description |
|---|---|---|
| `name` | *(required)* | filter name; also the form field name |
| `comparator` | `=` | comparison operator |
| `operator` | `and` | how this filter combines with the others |
| `formType` | `TextType` | form type of the field |
| `formOptions` | `[]` | options passed to the form type |

**`TextFilter`** — searches one value across several properties.

| Option | Default | Description |
|---|---|---|
| `comparator` | `like` | |
| `properties` | `[name]` | properties searched, combined with `OR` |

```php
new TextFilter(
    name: 'search',
    properties: ['title', 'content', 'author.name'],
    formOptions: ['label' => 'article.search'],
)
```

**`EntityFilter`** — filters on an association.

| Option | Default | Description |
|---|---|---|
| `formType` | `EntityType` | |
| `property` | the filter name | association filtered on |
| `multiple` | `false` | allow several values |

```php
new EntityFilter(
    name: 'tags',
    formOptions: ['class' => Tag::class, 'choice_label' => 'name'],
    multiple: true,
)
```

### Customising the filter form

Declaring filters is enough — the filter form is generated for you. Replace it when you need a
different layout or extra fields:

```php
new Index(
    filters: [/* … */],
    filterForm: ArticleFilterType::class,
    filterFormOptions: ['method' => 'GET'],
)
```

The submitted data becomes `$context['filters']`, which `FilterProvider` reads. A provider of
your own can read the same key and apply the values itself — filters that are not declared on
the operation are ignored by `FilterProvider` but still visible in the context.

### Writing a filter applicator

An applicator turns a filter and a value into query builder calls:

```php
namespace App\Filter\Applicator;

use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Metadata\FilterInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class DateRangeFilterApplicator implements FilterApplicatorInterface
{
    public function supports(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): bool
    {
        return $filter instanceof DateRangeFilter && $data instanceof QueryBuilder;
    }

    public function apply(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): void
    {
        // add the where clauses to $data
    }
}
```

Tag it with `lag_admin.filter_applicator` (the constant
`FilterApplicatorInterface::SERVICE_TAG`). The composite applies **every** applicator that
supports the filter, so keep `supports()` narrow.

Built-in applicators: `TextFilterApplicator` and `EntityFilterApplicator`, both in the Doctrine
bridge.

## Sorting

Sorting is opt-in per grid and per property:

```php
new Grid(
    properties: ['title', 'publishedAt'],
    sortable: true,
    sortParameter: 'sort',
    orderParameter: 'order',
)
```

```php
new Text(name: 'title', sortable: true, sortingPath: 'title')
```

Sortable headers render as links toggling `?sort={property}&order={asc|desc}`. `SortingProvider`
reads those from the context and adds the `ORDER BY`, creating the joins needed by a dotted
`sortingPath` (`author.lastName` joins `author`).

A default order that does not depend on the request goes in the operation context:

```php
new Index(context: ['order_by' => ['position' => 'ASC']])
```

## Pagination

On by default for collection operations:

| Option | Default | Description |
|---|---|---|
| `pagination` | `true` | wrap the query in a Pagerfanta pager |
| `itemsPerPage` | `25` | page size |
| `pageParameter` | `page` | query parameter carrying the page number |
| `limit` | *(none)* | hard cap on the number of records, used when pagination is off |

```php
new Index(itemsPerPage: 50)
new Index(pagination: false, limit: 4)   // "latest 4 articles" block
```

With pagination on, the provider returns a `Pagerfanta` instance and the index template renders
the pager through `pagerfanta()`. With pagination off, `ResultProvider` executes the query and
returns a collection, applying `limit` if set.

## Next

[Routing](routing.md).
