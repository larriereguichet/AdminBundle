# Routing

The bundle registers a routing loader of type `lag_admin`. At container compile time it walks
every resource, every operation, and adds one route per operation.

## Importing the loader

Once, and only once:

```php
// config/routes/lag_admin.php
return static function (RoutingConfigurator $routing): void {
    $routing->import('@LAGAdminBundle/config/routing.php');
};
```

Importing it twice throws `Do not add the Admin routing loader "lag_admin" twice`.

Because routes are compiled from metadata, **adding or renaming a resource or an operation
requires a cache clear**.

## Route names

Built from the `route_pattern` of the application (or the resource):

```
{application}.{resource}.{operation}   →   admin.article.update
```

Set `route_pattern` per application in `lag_admin.applications`, or per resource with
`routePattern`. An operation can also declare an explicit `route`.

## Paths

Unless the operation sets `path`, the path is generated:

| Operation kind | Generated path |
|---|---|
| collection | `/{plural-resource}/{operation}` — `/articles/index` |
| item | `/{plural-resource}/{identifier}/{operation}` — `/articles/{id}/update` |

The plural comes from the English inflector applied to `shortName`.

The identifiers used to build an item path are resolved in this order:

1. the operation's `identifiers`;
2. the resource's `identifiers`, which default to `['id']`.

A `Create` never gets an identifier segment — the record does not exist yet. Declaring several
identifiers produces one segment each, in order: `identifiers: ['author', 'id']` on a `Show`
gives `/books/{author}/{id}/show`.

`pathPrefix` on the resource replaces the pluralized segment:

```php
new Resource(
    shortName: 'article',
    pathPrefix: '/administration/articles',
    operations: [
        new Index(path: '/'),                  // /administration/articles
        new Show(path: '/{slug}'),             // /administration/articles/{slug}
        new Update(path: '/{slug}/edit'),      // /administration/articles/{slug}/edit
    ],
    identifiers: ['slug'],
)
```

When an operation sets `path` **and** the resource has a `pathPrefix`, the prefix is prepended.
Paths are lowercased.

Route parameters are derived from the operation identifiers when the path contains
placeholders; `routeParameters` overrides that mapping — `['tag' => 'slug']` fills the `{tag}`
placeholder from the record's `slug` property. Pass `routeParameters: []` for an operation that
takes no parameter at all (a "my account" screen, for instance, combined with
`identifiers: []`).

## Route defaults

Every generated route carries:

* `_controller` — the operation's controller;
* `_lag_operation` (configurable through `request_parameter`) — the full operation name.

That second default is what lets listeners and value resolvers rebuild the whole metadata graph
from a plain HTTP request.

## Methods

| Operation | Methods |
|---|---|
| `Index` | unrestricted |
| `Show` | `GET` |
| `Create` | `GET`, `POST` |
| `Update` | `GET`, `POST`, `PUT` |
| `Delete` | `GET`, `POST` |

Override with `methods: ['GET']`.

## Generating URLs

In Twig, three functions take an **operation name** rather than a route name, and resolve route
parameters from a record:

```twig
{{ lag_admin_path('admin.article.update', article) }}
{{ lag_admin_url('admin.article.index') }}
{{ lag_admin_link(link, article) }}
```

`lag_admin_path` returns a relative path, `lag_admin_url` an absolute URL, and `lag_admin_link`
renders the URL of a `Link` metadata object (handling its `url`, `route` or `operation`).

In PHP, inject `LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface`:

```php
$url = $this->operationUrlGenerator->generateUrl($operation, $article);
```

You can of course still use the plain Symfony generator with the generated route name:

```php
$this->router->generate('admin.article.update', ['id' => $article->getId()]);
```

## Linking to a non-admin route

A `Link` can point anywhere:

```php
new Link(name: 'docs', url: 'https://example.com/docs', text: 'Documentation')
new Link(name: 'home', route: 'app_homepage', text: 'Back to the site')
```

## Inspecting the generated routes

```bash
bin/console debug:router | grep admin.article
bin/console router:match /articles/index
```

## Next

[Security](security.md).
