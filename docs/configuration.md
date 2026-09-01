# Configuration

All configuration lives under the `lag_admin` extension key. This page documents every option.
Resource-level and operation-level options are documented in
[Resource options](reference/resource.md) and [Operation options](reference/operations.md).

## Full default configuration

```php
// config/packages/lag_admin.php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('lag_admin', [
        'mapping' => [
            'paths' => [
                param('kernel.project_dir').'/src/Entity',
            ],
        ],

        'applications' => [
            'admin' => [
                'date_format' => 'medium',
                'time_format' => 'short',
                'translation_domain' => 'messages',
                'translation_pattern' => '{application}.{resource}.{message}',
                'route_pattern' => '{application}.{resource}.{operation}',
                'base_template' => '@LAGAdmin/base.html.twig',
            ],
        ],

        'uploads' => [
            'storage' => 'lag_admin.media_storage',
            'media_directory' => param('kernel.project_dir').'/public/admin/media/uploads',
        ],

        'request_parameter' => '_lag_operation',

        'grid_templates' => [
            'table' => '@LAGAdmin/components/table_grid.html.twig',
            'card' => '@LAGAdmin/components/card_grid.html.twig',
        ],
    ]);
};
```

YAML works too, if you prefer it:

```yaml
lag_admin:
    mapping:
        paths:
            - '%kernel.project_dir%/src/Entity'
    applications:
        admin:
            translation_domain: admin
```

## `mapping.paths`

**Type:** `string[]` — **Default:** `['%kernel.project_dir%/src/Entity']`

Directories scanned for admin metadata. Two kinds of files are picked up:

* **PHP classes** carrying `#[Resource]` or `#[Grid]` attributes;
* **PHP configuration files** returning `static function (LAGAdminBuilder $builder): void { … }`.

A typical project uses both:

```php
'mapping' => [
    'paths' => [
        param('kernel.project_dir').'/src/Entity',        // attributes on entities
        param('kernel.project_dir').'/config/resources',  // resource definitions in config
    ],
],
```

Files that do not contain `return static function` are skipped, and files whose closure throws
are ignored silently — so a syntax-level mistake in a resource file shows up as a *missing
resource*, not as an error. See [Resources](concepts/resources.md#defining-resources-in-configuration-files).

## `applications`

**Type:** map of application name → options — **required**

An application groups resources that share routing, translation and layout conventions. A
resource belongs to exactly one application (`admin` by default) and its full name is
`{application}.{shortName}` — for example `admin.article`.

Every application a resource refers to **must be declared here** — referring to an undeclared
one throws `MissingApplicationException`.

```php
'applications' => [
    'admin' => [
        'translation_domain' => 'admin',
        'translation_pattern' => '{resource}.{message}',
        'base_template' => 'admin/base.html.twig',
    ],
    'shop' => [
        'translation_domain' => 'shop',
        'translation_pattern' => '{resource}.{message}',
        'base_template' => 'shop/base.html.twig',
    ],
],
```

| Option | Default | Description |
|---|---|---|
| `date_format` | `medium` | Intended as the default format for `Date` properties. **Currently unused** — the `Date` property attribute carries its own `dateFormat`. |
| `time_format` | `short` | Same remark. |
| `translation_domain` | `messages` | Translation domain used for labels, titles and messages of this application. |
| `translation_pattern` | `{application}.{resource}.{message}` | How translation keys are built. Set to `null` to use raw labels instead of translation keys. |
| `route_pattern` | `{application}.{resource}.{operation}` | How generated route names are built. |
| `base_template` | `@LAGAdmin/base.html.twig` | Layout every operation template extends. |

The same application can serve a public front-end: declaring a `shop` application with a
`shop/base.html.twig` layout is how you reuse the resource/grid machinery outside the back
office.

## `uploads`

| Option | Default | Description |
|---|---|---|
| `storage` | `lag_admin.media_storage` | Flysystem storage service used to store uploaded media. |
| `media_directory` | `%kernel.project_dir%/public/admin/media/uploads` | Local directory backing the default storage. |

The bundle prepends a Flysystem storage named after `uploads.storage`, backed by a `local`
adapter pointing at `uploads.media_directory`. Override the storage to push media elsewhere —
see [Uploads and images](customization/uploads.md).

## `request_parameter`

**Default:** `_lag_operation`

Name of the request attribute holding the current operation name. Generated routes set it as a
route default; listeners and value resolvers read it back to rebuild the operation metadata.
Change it only if it collides with something in your application.

## Metadata caching

Resource and grid metadata is built by scanning the filesystem and reflecting over classes, so
it is cached in the `lag_admin.cache` pool — a pool the bundle prepends onto `framework.cache`,
backed by `cache.app`. Clearing the Symfony cache clears it.

Caching is currently always enabled: the `cache`, `date_localization` and `filter_events`
options exist in the configuration tree but are not read by the extension. Do not rely on them.

## `grid_templates`

**Type:** map of grid type → template

```php
'grid_templates' => [
    'table' => '@LAGAdmin/components/table_grid.html.twig',
    'card' => '@LAGAdmin/components/card_grid.html.twig',
    'kanban' => 'admin/grids/kanban.html.twig',   // your own
],
```

A grid declares a `type`; the type resolves to a template through this map. A grid that sets its
own `template` bypasses the map entirely. See [Grids](concepts/grids.md).

## Configuration that the bundle prepends for you

You do not need to write any of this, but it is useful to know it happens — and that your own
configuration is merged on top:

| Extension | What is prepended |
|---|---|
| `framework` | cache pool `lag_admin.cache` based on `cache.app` |
| `validation` | auto-mapping on the bundle's metadata classes (metadata is validated as objects) |
| `twig` | form theme `@LAGAdmin/forms/theme.html.twig`, global `lag_admin` |
| `flysystem` | storage `%lag_admin.media_storage%` on a local adapter |
| `liip_imagine` | filter sets `lag_admin_thumbnail` and `lag_admin_full`, loader `lag_admin` |
| `babdev_pagerfanta` | Twig view, Bootstrap 5 template |
| `knp_menu` | menu template `@LAGAdmin/menu/menu-base.html.twig` |

## Autoconfiguration

Implementing one of these interfaces is enough — the tag is added automatically:

| Interface | Tag | Purpose |
|---|---|---|
| `State\Provider\ProviderInterface` | `lag_admin.state_provider` | supply data for an operation |
| `State\Processor\ProcessorInterface` | `lag_admin.state_processor` | persist data for an operation |
| `Request\ContextBuilder\ContextBuilderInterface` | `lag_admin.request_context_provider` | add entries to the operation context |
| `Grid\Provider\GridProviderInterface` | `lag_admin.grid_provider` | build grid metadata dynamically |
| `Grid\DataTransformer\DataTransformerInterface` | `lag_admin.data_transformer` | transform a value before rendering |
