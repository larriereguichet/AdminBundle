# Resources

A resource is one administered class. It declares which operations exist, which properties are
displayed, and the defaults every operation inherits.

## Declaring a resource with attributes

```php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use LAG\AdminBundle\Metadata\Attribute as LAG;

#[LAG\Resource(shortName: 'article')]
#[ORM\Entity]
class Article
{
    // ...
}
```

`resourceClass` is filled in automatically when the attribute sits on the class it describes, and
`shortName` defaults to the snake-cased class short name — `#[LAG\Resource]` alone on `Article`
gives `admin.article`. `application` defaults to `admin`.

`#[Resource]` is repeatable — the same entity can be exposed several times, typically once per
application:

```php
#[LAG\Resource(shortName: 'article', application: 'admin')]
#[LAG\Resource(shortName: 'article', application: 'shop', operations: [
    new LAG\Index(path: '/blog'),
    new LAG\Show(path: '/blog/{slug}', identifiers: ['slug']),
])]
class Article { }
```

## Defining resources in configuration files

For anything beyond a handful of options, a configuration file reads better and keeps the
entity clean. Put the file in a directory listed in `lag_admin.mapping.paths` and return a
closure taking a `LAGAdminBuilder`:

```php
// config/resources/admin/articles.php
declare(strict_types=1);

use App\Entity\Article;
use App\Form\Type\ArticleType;
use LAG\AdminBundle\Config\LAGAdminBuilder;
use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Attribute\Update;

return static function (LAGAdminBuilder $builder): void {
    $builder
        ->addResource('article', new Resource(
            application: 'admin',
            resourceClass: Article::class,
            pathPrefix: '/administration/articles',
            operations: [
                new Index(path: '/', grid: 'admin_articles'),
                new Create(),
                new Update(),
                new Delete(),
            ],
            properties: [
                new Text(name: 'title'),
                new Text(name: 'author'),
            ],
            form: ArticleType::class,
            translationPattern: 'article.{message}',
        ))
        ->addGrid('admin_articles', new Grid(
            type: 'table',
            properties: ['title', 'author'],
        ))
    ;
};
```

Notes:

* The name passed to `addResource()` wins over `shortName` — the builder calls
  `withShortName()` on the object it receives.
* `$builder->env()` gives you the kernel environment, if you need to register a resource only
  in `dev`.
* The file must contain the literal string `return static function`, otherwise the scanner
  skips it. A closure that throws is skipped silently, so a resource that fails to appear is
  usually a broken configuration file.

## Naming

| Concept | Pattern | Example |
|---|---|---|
| Resource name | `{application}.{shortName}` | `admin.article` |
| Operation name | `{application}.{resource}.{operation}` | `admin.article.index` |
| Route name | `route_pattern` of the application | `admin.article.index` |
| Translation key | `translation_pattern` of the application or resource | `article.title` |

`shortName` and `application` must match `/^[a-z][a-z0-9_]*$/` — lowercase letters, digits and
underscores, starting with a letter.

## Operations

`operations` defaults to the five standard ones:

```php
new Index(), new Show(), new Create(), new Update(), new Delete()
```

Passing your own list replaces that default entirely — list every operation you want:

```php
new Resource(
    shortName: 'article',
    operations: [
        new Index(grid: 'admin_articles'),
        new Update(),
    ],
)
```

See [Operations](operations.md).

## Properties

Properties can come from three places, merged in this order:

1. **Attributes** — `#[LAG\Text]`, `#[LAG\Date]`, … on the class or on its fields;
2. **Introspection** — if, and only if, *no* attribute declares a single property, the bundle
   derives them from the Doctrine mapping (`string` → `Text`, `text` → `RichText`, `boolean` →
   `Boolean`, date types → `Date`), falling back to PHP reflection when the class is not managed
   by Doctrine;
3. **The resource `properties` argument** — declared in the resource itself, and taking
   precedence over both.

```php
class Article
{
    #[LAG\Text]                       // from the field
    private ?string $title = null;
}

new Resource(
    shortName: 'article',
    properties: [
        new Text(name: 'title', length: 50),   // wins over the attribute above
    ],
)
```

A property with no explicit `label` gets one from the translation pattern; with no explicit
`propertyPath`, the path is its name. See [Properties](properties.md).

## Defaults inherited by operations

Most resource options are just defaults for the operations below them. An operation that sets
the option itself always wins.

| Resource option | Inherited by |
|---|---|
| `provider`, `processor` | operations that do not set their own |
| `permissions` | operation permissions |
| `identifiers` | operation identifiers, route parameters and paths |
| `form`, `formOptions`, `formTemplate` | `Create` and `Update` |
| `validation`, `validationContext` | all operations |
| `ajax` | all operations |
| `normalizationContext`, `denormalizationContext` | all operations |
| `context` | merged into the operation context |
| `translationPattern`, `translationDomain` | labels and titles |
| `routePattern` | generated route names |
| `pathPrefix` | prepended to every operation path |

## Identifiers

`identifiers` defaults to `['id']`. It drives:

* the path of item operations — `/articles/{id}/update`;
* the route parameters generated for links;
* the `WHERE` clause added by the ORM provider;
* the fields excluded from the generated form.

Use a natural key when your URLs need one:

```php
new Resource(
    shortName: 'article',
    identifiers: ['slug'],
    operations: [
        new Show(path: '/{slug}'),
    ],
)
```

With Doctrine, identifiers are also introspected from the ORM mapping when you do not set them.

## Grouping and presentation

| Option | Effect |
|---|---|
| `group` | groups the resource under a sub-menu in the sidebar, labelled `lag_admin.menu.group.{group}` |
| `title` | human title of the resource; available as metadata, not used by the built-in menus |
| `icon` | icon name; available as metadata, not used by the built-in menus |

The sidebar lists every resource that has at least one collection operation, labelling each
entry `lag_admin.menu.{pluralized_short_name}`. See [Menus](menus.md).

## Complete option list

See [Resource options](../reference/resource.md).

## Next

[Operations](operations.md).
