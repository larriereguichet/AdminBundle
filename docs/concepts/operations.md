# Operations

An operation is one action on a resource: a route, a controller, a provider, a processor and a
template. Five classes ship with the bundle.

| Operation | Kind | Default path | Default methods | Controller | Provider |
|---|---|---|---|---|---|
| `Index` | collection | `/{resources}/index` | any | `IndexResources` | `ORMProvider` |
| `Show` | item | `/{resources}/{id}/show` | `GET` | `ShowResource` | `ORMProvider` |
| `Create` | item | `/{resources}/create` | `GET`, `POST` | `ProcessResource` | `CreateProvider` |
| `Update` | item | `/{resources}/{id}/update` | `GET`, `POST`, `PUT` | `ProcessResource` | `ORMProvider` |
| `Delete` | item | `/{resources}/{id}/delete` | `GET`, `POST` | `ProcessResource` | `ORMProvider` |

`Index` extends `CollectionOperation`: it adds pagination, sorting, filters, grid and batch
options. Everything else extends `Operation`.

## Collection operations versus item operations

The distinction drives several defaults:

* **path** — collection operations get `/{plural}/{operation}`, item operations get
  `/{plural}/{identifier}/{operation}`;
* **title** — collection operations are titled with the pluralized resource name (`Articles`),
  item operations with `{operation} {resource}` (`Update Article`);
* **links** — `Index` gets a contextual *create* link and per-row *update* / *delete* links;
* **provider decorators** — pagination, sorting and filtering only apply to collection
  operations.

## Overriding an operation

Every default can be replaced by naming the argument:

```php
new Index(
    title: 'article.articles',
    template: 'admin/articles/index.html.twig',
    path: '/',
    provider: PublishedArticlesProvider::class,
    itemsPerPage: 50,
    grid: 'admin_articles',
)
```

## Several operations of the same class

Give each one a `name`. The name becomes part of the operation and route names:

```php
new Resource(
    shortName: 'order',
    operations: [
        new Index(path: '/', grid: 'admin_orders'),
        new Index(
            name: 'cancelled_orders',
            title: 'order.cancelled_orders',
            path: '/cancelled',
            provider: CancelledOrdersProvider::class,
            grid: 'admin_orders',
        ),
    ],
)
```

You get `admin.order.index` and `admin.order.cancelled_orders`, each with its own route.

The same trick turns `Update` into any state-changing action — an operation that flips a flag,
triggers a workflow transition, or sends an email is just an `Update` with a name, a path and a
processor:

```php
new Update(
    name: 'publish',
    title: 'article.publish',
    path: '/{id}/publish',
    form: FormType::class,
    processor: PublishArticleProcessor::class,
    redirectOperation: 'index',
    flashMessage: 'article.published',
)
```

## Titles and templates

| Option | Default |
|---|---|
| `title` | derived from the resource and operation names; pass `false` to render no title |
| `template` | `@LAGAdmin/resources/{operation}.html.twig` for the five built-ins |
| `baseTemplate` | the application's `base_template` |
| `embedded` | when `true`, the base template becomes `@LAGAdmin/partial.html.twig` |

`embedded: true` is how you render an operation inside another page — the response contains only
the operation's own markup, with no layout. Combined with a dedicated route, it is the basis for
partial/AJAX rendering.

## Context

`context` is an arbitrary array handed to the provider and the processor. It is the clean way to
parameterize a shared provider:

```php
new Index(
    name: 'cancelled_orders',
    context: ['state' => 'cancelled'],
    provider: StateOrderProvider::class,
)
```

```php
public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
{
    return $this->repository->createQueryBuilderForState($context['state']);
}
```

The resource `context` and the operation `context` are merged, the operation winning. Context
builders add more entries at runtime — `page`, `sort`, `order`, `filters`, `partial`, `json`.

Two context keys are understood by the Doctrine bridge out of the box:

```php
context: [
    'criteria' => ['enabled' => true],        // added as WHERE clauses
    'order_by' => ['position' => 'ASC'],      // added as ORDER BY
]
```

Both only apply when the provider returns a Doctrine query builder. `order_by` accepts a
property name (its `sortingPath` is used, and joins are created for dotted paths).

## Redirections

After a successful `Create`, `Update` or `Delete`, the response handler redirects. It picks, in
order:

1. `redirectRoute` + `redirectRouteParameters`, when set;
2. `redirectOperation` — an operation name; a short name like `index` is expanded to
   `{application}.{resource}.index`;
3. the resource's `Index` operation, if there is one.

```php
new Update(
    redirectOperation: 'index',
)
```

## Flash messages

`Create`, `Update` and `Delete` show a flash message on success:
`lag_admin.ui.create_success`, `lag_admin.ui.process_success`, `lag_admin.ui.delete_success`.
Override it per operation with `flashMessage`, or pass `null` to keep quiet.

## Links

Three link buckets are attached to an operation:

| Option | Where it is rendered | Available on |
|---|---|---|
| `contextualLinks` | the contextual menu in the page header (KnpMenu, `@LAGAdmin/menu/horizontal.html.twig`) | every operation |
| `itemLinks` | one set per grid row | collection operations |
| `collectionLinks` | above the grid, in `@LAGAdmin/resources/index.html.twig` | collection operations |

`Index` gets sensible defaults: a *create* contextual link and *update* / *delete* item links,
when those operations exist. Setting either option replaces the whole default list.

```php
new Index(
    contextualLinks: [
        new Link(
            operation: 'create',
            text: 'order.create',
            icon: 'bi:circle-plus',
            attributes: ['class' => 'btn btn-primary'],
        ),
    ],
    itemLinks: [
        new Link(
            operation: 'prepare',
            text: 'order.prepare',
            icon: 'bi:basket',
            condition: 'workflow.can(this, "prepare")',
            workflow: 'order',
        ),
        'delete',   // shorthand for new Link(operation: 'delete')
    ],
)
```

An operation name without a dot is resolved against the current resource; use the full name
(`shop.article.show`) to link across resources.

`condition` is a Symfony ExpressionLanguage expression evaluated per row. Available variables:
`this` and `data` (the row), `object`, `resource` (the row data), `auth_checker`, plus `workflow`
when the link declares one. See [Workflow](../customization/workflow.md).

## Batch operations

An `Index` can expose checkboxes and apply another operation to the selection:

```php
new Index(
    grid: 'admin_articles',
    batchOperations: ['delete', 'publish'],
)
```

For each checked identifier, the target operation's provider fetches the record and its
processor processes it, then the user is redirected back.

## Permissions

`permissions` is a list of roles; access is granted when the user has **at least one** of them.
Unset, the operation inherits the resource permissions; empty, everybody is allowed. See
[Security](security.md).

## Serialization and mapping

| Option | Purpose |
|---|---|
| `normalizationContext` / `denormalizationContext` | contexts passed to the Serializer |
| `input` / `output` | DTO classes to map from/to, using `symfony/object-mapper` |
| `normalizationInput` / `normalizationOutput` | classes used by the normalization decorators |

These are only active when the matching decorators apply. `input` / `output` need
`symfony/object-mapper` installed as a **non-dev** dependency: FrameworkBundle only registers
the object mapper service then, and `ObjectMapperCompilerPass` removes `MappingProvider` and
`MappingProcessor` when it finds none.

## Complete option list

See [Operation options](../reference/operations.md).

## Next

[Properties](properties.md).
