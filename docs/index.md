# Documentation

LAG AdminBundle generates an administration interface from PHP attributes: declare a
`#[Resource]` on a Doctrine entity, and the bundle builds the routes, the controllers, the
grids, the forms and the menus from that metadata at container compile time.

## Start here

| Page | Answers |
|---|---|
| [Installation](installation.md) | Requirements, dependencies, routing, assets, security, first configuration |
| [Getting started](getting-started.md) | One entity to a working administration screen, in one page |
| [Configuration](configuration.md) | Every `lag_admin` key, its default and its effect |

## Concepts

One page per feature: what it is, how it behaves, and where its exhaustive option list lives.

| Page | Covers |
|---|---|
| [Overview](concepts/overview.md) | The mental model — application, resource, operation, property — and the request lifecycle |
| [Resources](concepts/resources.md) | Declaring an administered class, with attributes or configuration files |
| [Operations](concepts/operations.md) | Index, Show, Create, Update, Delete and custom operations; links, batches, mapping |
| [Properties](concepts/properties.md) | How a field is displayed and how its form type is guessed |
| [Grids](concepts/grids.md) | Laying out a collection: columns, order, sorting, templates |
| [Forms](concepts/forms.md) | Generated forms, explicit form types, validation |
| [Filters](concepts/filters.md) | Filtering, sorting and searching a collection |
| [Providers and processors](concepts/providers-and-processors.md) | Where the data comes from and what happens on submission |
| [Routing](concepts/routing.md) | The generated routes, their names, their parameters |
| [Security](concepts/security.md) | Permissions on resources, operations and properties |
| [Menus](concepts/menus.md) | The sidebar, contextual and user menus |
| [Events](concepts/events.md) | The lifecycle events and what a listener may do with them |
| [Translations](concepts/translations.md) | Translation keys, patterns and domains |

## Reference

Exhaustive option lists — every argument of every attribute, with its type and default.

| Page | Covers |
|---|---|
| [Resource options](reference/resource.md) | `Resource` |
| [Operation options](reference/operations.md) | `Index`, `Show`, `Create`, `Update`, `Delete`, `Link` |
| [Property options](reference/properties.md) | Every property class, with its per-class defaults |
| [Filter options](reference/filters.md) | `Filter`, `TextFilter`, `EntityFilter`, applicators |
| [Grid options](reference/grid.md) | `Grid`, the built-in types, the view model |
| [Twig reference](reference/twig.md) | Globals, functions, filters, components, template variables |

## Customization

How to replace, decorate or override — with the code to copy.

| Page | Covers |
|---|---|
| [Overview](customization/overview.md) | The extension points, and which one to reach for |
| [Templates](customization/templates.md) | Overriding any template the bundle renders |
| [Twig components](customization/twig-components.md) | Swapping a component, writing a cell component |
| [Custom properties](customization/custom-properties.md) | New property types and data transformers |
| [State](customization/state.md) | Custom providers and processors |
| [Uploads](customization/uploads.md) | File storage through Flysystem, thumbnails through LiipImagine |
| [Workflow](customization/workflow.md) | Driving operations with `symfony/workflow` |
