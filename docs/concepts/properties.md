# Properties

A property describes how one field of a resource is displayed in a grid and, when no form type
is given, how it is guessed in a form. Property classes live in
`LAG\AdminBundle\Metadata\Attribute` and all extend `Property`.

## Declaring properties

On the entity field:

```php
use LAG\AdminBundle\Metadata\Attribute as LAG;

class Article
{
    #[LAG\Text(length: 60)]
    private ?string $title = null;

    #[LAG\Date(dateFormat: 'short')]
    private ?\DateTimeImmutable $publishedAt = null;

    #[LAG\Boolean]
    private bool $published = false;
}
```

Property attributes are repeatable on a field — useful when the same value is rendered twice,
for instance as a public link and as an admin link:

```php
#[LAG\Link(propertyPath: '.', operation: 'shop.article.show', textPath: 'title', label: false)]
#[LAG\Link(name: 'admin_title', propertyPath: '.', operation: 'admin.article.update', textPath: 'title')]
private ?string $title = null;
```

The first attribute takes the field name; the others need an explicit `name`.

Or in the resource definition, where a property does not have to match a field at all:

```php
new Resource(
    shortName: 'article',
    properties: [
        new Text(name: 'title'),
        new Property(
            name: 'price',
            propertyPath: true,
            template: 'admin/articles/grid/price.html.twig',
        ),
    ],
)
```

## Common options

Every property accepts these:

| Option | Default | Description |
|---|---|---|
| `name` | the field name | identifier used in grids and forms |
| `propertyPath` | the name | Symfony PropertyAccess path; `true` or `'.'` passes the whole row object; `false` passes nothing |
| `label` | from the translation pattern | column header; `false` renders no label |
| `template` | per property type | Twig template rendering the cell |
| `component` | per property type | Twig component rendering the cell, when it uses one |
| `sortable` | `true` (varies) | whether the column header is a sort link |
| `sortingPath` | the property path | field actually sorted on |
| `translatable` | `true` (varies) | whether the rendered value is passed through the translator |
| `translationDomain` | the resource's | domain used for the label and the value |
| `attributes` | `[]` | HTML attributes of the cell |
| `rowAttributes` | `[]` | HTML attributes of the row |
| `headerAttributes` | `[]` | HTML attributes of the header cell |
| `dataTransformer` | per property type | service transforming the raw value before rendering |
| `permissions` | *(none)* | roles required to see the cell |
| `condition` | *(none)* | ExpressionLanguage expression deciding whether the cell is rendered |

### `propertyPath`

The single most useful option. Three shapes:

```php
new Text(name: 'author', propertyPath: 'author.name')   // traverse
new Property(name: 'price', propertyPath: true)         // the whole row object
new Image(name: 'thumbnail', propertyPath: 'images.first')
```

`propertyPath: true` (or `'.'`) is what you use with a custom template that needs the entity
itself rather than one of its values.

### `condition`

An expression evaluated for each cell:

```php
new Property(
    name: 'price',
    propertyPath: true,
    template: 'shop/products/grid/price.html.twig',
    condition: 'is_granted("ROLE_CUSTOMER")',
)

new Property(
    name: 'storage_days',
    propertyPath: true,
    template: 'shop/products/grid/storage_days.html.twig',
    condition: 'this.getStorageDays() !== null',
)
```

Available variables: `this` and `data` (the value being rendered), `object`, `resource` (the row
data), `auth_checker`, and the standard `is_granted()` function.

### `permissions`

A shortcut for role checks: the cell renders when the user has at least one of the listed roles.

## The property types

### `Property`

The generic one. It renders through the template you give it, with no formatting of its own.
Reach for it whenever a built-in type does not fit.

```php
new Property(
    name: 'price',
    propertyPath: true,
    template: 'admin/articles/grid/price.html.twig',
)
```

```twig
{# admin/articles/grid/price.html.twig #}
<p {{ attributes }}>{{ (data.price / 100)|format_currency('EUR') }}</p>
```

The template receives `data` (the value resolved from the property path), `property`
(the metadata) and `attributes`.

### `Text`

Truncated plain text.

| Option | Default | Description |
|---|---|---|
| `length` | `100` | maximum number of characters |
| `replace` | `...` | ellipsis appended when truncated |
| `empty` | `~` | rendered when the value is empty |
| `prefix`, `suffix` | `''` | wrapped around the value |

### `Title`

Same options as `Text`, rendered as a heading. Intended for the main label of a row.

### `RichText`

Same options as `Text`, but the value is rendered as HTML. Quill Delta documents are converted
through the QuillJs bridge — see the `lag_admin_rich_text` Twig filter.

### `Boolean`

Renders a yes/no indicator.

### `Date`

| Option | Default | Description |
|---|---|---|
| `dateFormat` | `medium` | `none`, `short`, `medium`, `long`, `full`, or an ICU pattern |
| `timeFormat` | `none` | same values, for the time part |

Formatting goes through Twig's `format_datetime`, so it is locale aware.

### `Map`

Maps raw values to labels — the natural fit for enums and status columns.

```php
new Map(
    name: 'status',
    dataTransformer: EnumDataTransformer::class,
    map: [
        'published' => 'article.status.published',
        'draft' => 'article.status.draft',
    ],
)
```

`EnumDataTransformer` converts a PHP enum to its backing value first. In a form, a `Map`
property is guessed as a `ChoiceType` built from the map.

### `Count`

Renders the size of a collection, through `CountDataTransformer`.

### `Collection`

Renders each entry of a collection with an inner property.

```php
new Collection(
    name: 'tags',
    entryProperty: new Text(
        propertyPath: 'name',
        attributes: ['class' => 'badge bg-info-subtle'],
    ),
)
```

### `Compound` and `Group`

Both render several properties inside one cell.

```php
new Group(
    name: 'card_body',
    label: false,
    attributes: ['class' => 'card-body'],
    properties: ['name', 'price'],
)
```

`Group` references other properties of the resource by name — it is how card grids are laid out.
`Compound` embeds property objects directly.

### `Image`

| Option | Default | Description |
|---|---|---|
| `imageFilter` | *(none)* | LiipImagine filter set applied to the thumbnail |
| `storage` | the default media storage | Flysystem storage holding the file |
| `upload` | `true` | whether the property accepts uploads in forms |

```php
new Image(
    name: 'thumbnail',
    propertyPath: 'images.first',
    imageFilter: 'article_thumbnail',
    upload: false,
)
```

See [Uploads and images](../customization/uploads.md).

### `Link`

Renders an anchor. This is how you make a column clickable and how you build row actions.

| Option | Description |
|---|---|
| `operation` | target operation name; a short name is resolved against the current resource |
| `route` / `routeParameters` | target a plain Symfony route instead |
| `url` | a raw URL |
| `text` | link text (translated) |
| `textPath` | property path read on the row to build the text |
| `icon` | icon name, e.g. `bi:pencil` |
| `type` | free-form marker available to templates |
| `workflow`, `workflowTransition` | make the link depend on a workflow transition |

```php
new Link(
    name: 'title',
    propertyPath: '.',
    operation: 'admin.article.update',
    textPath: 'title',
    attributes: ['class' => 'link-primary'],
)
```

Route parameters are derived from the target operation's identifiers, read off the row.

### `Slug`

Displays a slug and, more importantly, declares that the field is generated:

```php
new Slug(name: 'slug', source: 'title')
```

On every write, `GenerateSlugListener` fills the property from `source` (a property name or a
list of them) using the named slugger.

### `Form`

Embeds a form inside a grid cell — an "add to cart" button in a product card, a quick inline
edit.

```php
new Form(
    name: 'add_to_cart',
    propertyPath: true,
    dataTransformer: ProductToOrderItemDataTransformer::class,
    form: AddToCartType::class,
    formTemplate: 'shop/products/grid/add_to_cart.html.twig',
    condition: 'is_granted("ROLE_CUSTOMER")',
)
```

### `ResourceLink`

A `Text` that links to an operation of *another* resource, addressed by
`application` + `resource` + `operation`.

## Labels and translation

With a `translationPattern` on the application or the resource, a property with no explicit
label gets the key built from that pattern:

```
translation_pattern: '{resource}.{message}'   →  article.published_at
```

Without a pattern, the label is the humanized property name (`Published At`). Pass
`label: false` to render no header at all. See [Translations](translations.md).

## Sorting

A sortable property renders its header as a sort link toggling `?sort=…&order=…`. The sorted
field is `sortingPath`, defaulting to the property path — set it explicitly when the displayed
value is not the sortable one:

```php
new Text(name: 'author', propertyPath: 'author.name', sortingPath: 'author.lastName')
```

Sorting has to be enabled on the grid too (`sortable: true`), and the provider must return a
Doctrine query builder for the sorting decorator to apply.

## Data transformers

A data transformer converts the raw value before rendering:

```php
namespace App\Grid\DataTransformer;

use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;

final readonly class MoneyDataTransformer implements DataTransformerInterface
{
    public function transform(PropertyInterface $property, mixed $data): mixed
    {
        return $data === null ? null : $data / 100;
    }
}
```

```php
new Text(name: 'price', dataTransformer: MoneyDataTransformer::class)
```

The interface is autoconfigured — no tag to add. Built-in transformers: `EnumDataTransformer`,
`CountDataTransformer`, `FormDataTransformer`, and `ImageDataTransformer` from the LiipImagine
bridge.

## Complete option list

See [Property options](../reference/properties.md).

## Next

[Grids](grids.md).
