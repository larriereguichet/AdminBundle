# Overview

## The mental model

Everything in the bundle hangs off three metadata objects.

```
Application  (admin, shop, …)          declared in config/packages/lag_admin.php
  └── Resource  (admin.article)        one administered class
        ├── Operation  (index, show, create, update, delete, …)
        │     └── one route, one controller, one provider, one processor, one template
        └── Property   (title, publishedAt, …)
              └── how one field is rendered in a grid and guessed in a form
```

Alongside them, a **Grid** describes how a collection of resources is laid out (which
properties, in which order, with which template).

Nothing is generated at runtime: metadata is read at container compile time, routes are built
from it, and the result is cached.

## The three metadata objects

**Resource** — `LAG\AdminBundle\Metadata\Attribute\Resource`. The top-level descriptor. It
names the resource (`shortName`), says which class it administers (`resourceClass`), which
application it belongs to, and holds its operations and properties. Its full name is
`{application}.{shortName}`.

**Operation** — one action on a resource. Five concrete classes ship with the bundle: `Index`,
`Show`, `Create`, `Update`, `Delete`. `Index` is a *collection* operation (it works on many
records); the others are *item* operations. Each carries its own route, path, template, form,
provider, processor and permissions, all defaulted from the resource.

**Property** — how a single field is displayed. `Text`, `Date`, `Boolean`, `Image`, `Link`,
`Collection`, `Map`, `Count`, `RichText`, `Slug`, `Title`, `Group`, `Compound`, `Form`, and the
generic `Property`. Properties feed both the grid and the form guesser.

## The request lifecycle

```
HTTP request
  → routing            route generated from the operation metadata; it carries
                       _lag_operation = "admin.article.index" as a route default
  → listener           InitializeResourceContextListener puts the operation in the
                       resource context
  → listener           AccessListener denies access if the operation's permissions
                       are not granted
  → value resolvers    ResourceMetadataInterface / OperationMetadataInterface /
                       GridMetadataInterface are injected into the controller
  → controller         IndexResources, ShowResource or ProcessResource
  → context builders   page, sort, filters, criteria, embedded flag → $context
  → provider           ProviderInterface::provide($operation, $urlVariables, $context)
  → form               created and handled when the operation has one
  → processor          ProcessorInterface::process(...) on a valid submission
  → event              ResourceControllerEvent — a listener may return its own response
  → response handler   template render, JSON, or redirect
  → grid builder       GridBuilder → RowBuilder → CellBuilder → GridView
  → Twig               rendered through Twig components
```

Three controllers cover every operation:

| Controller | Used by | Does |
|---|---|---|
| `IndexResources` | `Index` | filter form, batch form, provider, grid building |
| `ShowResource` | `Show` | provider, render |
| `ProcessResource` | `Create`, `Update`, `Delete` | provider, form, processor, redirect |

You will rarely replace them. Add behaviour by decorating a provider or a processor, or by
listening to `ResourceControllerEvent` — see [Custom state](../customization/state.md) and
[Events](events.md).

## Composition, not inheritance

Providers and processors are composed twice over:

* a **composite** picks the concrete implementation whose class name matches the operation's
  `provider` / `processor` option;
* **decorators** wrap the composite to add cross-cutting behaviour — URL variables, criteria,
  sorting, pagination, filtering, serialization, validation, workflow transitions, flash
  messages, events.

The practical consequence: a custom provider that returns a Doctrine `QueryBuilder` still gets
filtering, sorting and pagination for free, because those live in decorators around it.

## Where metadata comes from

Two interchangeable sources, both scanned from `lag_admin.mapping.paths`:

* **PHP attributes** on the entity — the fastest way to get going, best when the admin
  configuration is small and closely tied to the class;
* **PHP configuration files** returning a closure that receives a `LAGAdminBuilder` — better
  when a resource has many operations, or when the same entity is exposed twice (a back office
  and a public front-end, for instance).

Both produce exactly the same objects. See [Resources](resources.md).

## Next

[Resources](resources.md) — declaring what is administered.
