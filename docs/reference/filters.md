# Reference — Filter options

Filters are declared in a collection operation's `filters` option. All classes live in
`LAG\AdminBundle\Metadata\Attribute`.

## `Filter`

The base class: compares one property with one submitted value.

| Option | Type | Default | Description |
|---|---|---|---|
| `name` | `string` | *(required)* | Filter name, also the form field name. |
| `comparator` | `string` | `=` | Comparison operator. |
| `operator` | `string` | `and` | How this filter combines with the others. |
| `formType` | `string` | `TextType::class` | Form type of the field. |
| `formOptions` | `array` | `[]` | Options passed to the form type. |

## `TextFilter`

Searches one value across several properties.

| Option | Type | Default | Description |
|---|---|---|---|
| `comparator` | `string` | `like` | |
| `properties` | `?array<string>` | `null` | Properties searched. At least one; each a non-empty string. Defaults to the filter name. |

```php
new TextFilter(
    name: 'search',
    properties: ['title', 'content'],
    formOptions: ['label' => 'article.search'],
)
```

## `EntityFilter`

Filters on a Doctrine association.

| Option | Type | Default | Description |
|---|---|---|---|
| `formType` | `string` | `EntityType::class` | |
| `property` | `?string` | the filter name | Association filtered on. Required (defaulted from the name). |
| `multiple` | `bool` | `false` | Allow several values; also sets the form option. |

```php
new EntityFilter(
    name: 'tags',
    formOptions: ['class' => Tag::class, 'choice_label' => 'name'],
    multiple: true,
)
```

## How a filter is applied

1. `CollectionOperationMetadataFactory` builds the filter form — `FilterType` unless the
   operation names another one — with one field per filter.
2. `IndexResources` handles the form; the submitted data becomes `$context['filters']`.
3. `FilterProvider` iterates over `$context['filters']`, skipping values whose name is not a
   declared filter, and asks the filter applicator to apply each one to the query builder.
4. `CompositeFilterApplicator` applies **every** applicator whose `supports()` returns true.

Filtering only applies when the provider returns a Doctrine `QueryBuilder`.

## Writing an applicator

```php
namespace App\Filter\Applicator;

use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Metadata\FilterInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class DateRangeFilterApplicator implements FilterApplicatorInterface
{
    public function supports(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): bool;
    public function apply(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): void;
}
```

Tag it `lag_admin.filter_applicator` — the constant is
`FilterApplicatorInterface::SERVICE_TAG`.

Built-in applicators, both in the Doctrine bridge: `TextFilterApplicator`,
`EntityFilterApplicator`.
