# Grids

A grid describes how a collection is laid out: which properties, in which order, with which
markup. Collection operations render one.

## Declaring a grid

As an attribute on the entity:

```php
use LAG\AdminBundle\Metadata\Attribute as LAG;

#[LAG\Resource(shortName: 'article', operations: [new LAG\Index(grid: 'admin_articles')])]
#[LAG\Grid(
    name: 'admin_articles',
    title: 'Articles',
    properties: ['title', 'author', 'publishedAt'],
)]
class Article { }
```

Or in a configuration file:

```php
return static function (LAGAdminBuilder $builder): void {
    $builder
        ->addResource('article', new Resource(/* … */))
        ->addGrid('admin_articles', new Grid(
            type: 'table',
            properties: ['title', 'author', 'publishedAt'],
        ))
    ;
};
```

Both are repeatable and both live in `lag_admin.mapping.paths`. Grids are global: any operation
of any resource can reference any grid by name.

## Wiring a grid to an operation

```php
new Index(grid: 'admin_articles')
```

**A collection operation without a `grid` renders no grid at all.** The value resolver only
injects a grid when the operation names one, and the index template skips the block when there
is none. This is the most common reason for an empty-looking index page.

## Choosing the markup

Two knobs, in order of precedence:

1. **`template`** — a Twig template rendered for the whole grid;
2. **`type`** — a key resolved through `lag_admin.grid_templates`.

```php
new Grid(type: 'table')                                    // @LAGAdmin/components/table_grid.html.twig
new Grid(type: 'card')                                     // @LAGAdmin/components/card_grid.html.twig
new Grid(template: 'admin/articles/grids/timeline.html.twig')
```

`type` defaults to `table`. The grid is rendered through the resolved **template** — the
`component` option exists on the metadata but `GridBuilder` does not pass it to the view, so
setting it has no effect today. Register your own types in the configuration:

```php
'grid_templates' => [
    'table' => '@LAGAdmin/components/table_grid.html.twig',
    'card' => '@LAGAdmin/components/card_grid.html.twig',
    'kanban' => 'admin/grids/kanban.html.twig',
],
```

## Choosing the columns

`properties` is an ordered list of property **names**, resolved against the resource's
properties:

```php
new Grid(properties: ['thumbnail', 'title', 'tags', 'price'])
```

Leave it empty to render every property of the resource, in declaration order.

Because it is just a list of names, the same resource can feed several grids showing different
subsets — a dense table for the back office, a card layout for the shop.

## Grid options

| Option | Default | Description |
|---|---|---|
| `name` | *(required)* | grid identifier, referenced by operations |
| `title` | *(none)* | grid title; `false` renders none |
| `type` | `table` | resolves to a template through `grid_templates` |
| `template` | *(from the type)* | explicit template, bypassing `type` |
| `component` | `null` | Twig component used to render the grid, instead of `template` |
| `properties` | all | ordered list of property names |
| `attributes` | `[]` | HTML attributes of the grid element |
| `rowAttributes` | `[]` | HTML attributes applied to rows |
| `headerRowAttributes` | `[]` | HTML attributes of the header row |
| `headerAttributes` | `[]` | HTML attributes of header cells |
| `titleAttributes` | `[]` | HTML attributes of the title |
| `options` | `[]` | free-form options handed to the template |
| `emptyMessage` | `lag_admin.ui.no_record` | shown when the collection is empty |
| `useHeaders` | `true` | render the header row |
| `sortable` | `false` | enable sorting links on sortable properties |
| `sortParameter` | `sort` | query parameter carrying the sorted property |
| `orderParameter` | `order` | query parameter carrying the direction |
| `form`, `formOptions` | `FormType` | form wrapping the grid, when the operation submits rows |

## How a grid is built

```
GridBuilder      builds a GridView: title, attributes, headers, rows
  └── RowBuilder     one RowView per record, plus the header row
        └── CellBuilder    one CellView per property
```

`CellBuilder` is itself a chain of decorators, each handling one concern:

| Builder | Responsibility |
|---|---|
| `SecurityCellBuilder` | skips the cell when `permissions` are not granted |
| `ConditionCellBuilder` | skips the cell when `condition` evaluates to false |
| `CompoundCellBuilder` | renders `Compound` and `Group` properties |
| `CollectionCellBuilder` | renders `Collection` properties entry by entry |
| `DataCellBuilder` | resolves the property path and applies the data transformer |

The result is a plain view model (`GridView` → `RowView` → `CellView`) handed to Twig. Nothing
in the view layer queries the database or the container.

## Rendering

The index template renders the grid through a Twig component:

```twig
<twig:lag_admin:grid grid="{{ grid }}" data="{{ data }}" />
```

which in turn renders `@LAGAdmin/components/row.html.twig` and
`@LAGAdmin/components/cell.html.twig` per row and per cell. Each cell renders either its
property `template` or its property `component`.

To take over the markup, override a template or a component — see
[Templates](../customization/templates.md) and
[Twig components](../customization/twig-components.md).

## Building grids dynamically

When the columns depend on runtime data, implement a `GridProviderInterface`:

```php
namespace App\Grid\Provider;

use LAG\AdminBundle\Grid\Provider\GridProviderInterface;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\GridMetadataInterface;

final readonly class ReportGridProvider implements GridProviderInterface
{
    public function supports(string $gridName): bool
    {
        return str_starts_with($gridName, 'report_');
    }

    public function provide(string $gridName): GridMetadataInterface
    {
        return new Grid(
            name: $gridName,
            properties: $this->columnsFor($gridName),
        );
    }
}
```

The interface is autoconfigured. Providers are consulted before the static metadata, so a
provider that `supports()` a name wins over a declared grid of the same name.

## Next

[Providers and processors](providers-and-processors.md).
