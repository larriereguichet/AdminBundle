# Installation

## Requirements

| Requirement | Version |
|---|---|
| PHP | 8.4 or higher |
| Symfony | 7.0 or higher |
| Doctrine ORM | required for the default provider and processor |

## 1. Install the package

```bash
composer require lag/adminbundle
```

The following bundles are installed as dependencies and are required at runtime:

| Bundle | Why |
|---|---|
| `doctrine/doctrine-bundle` + ORM | default provider, processor, identifier and property introspection |
| `knplabs/knp-menu-bundle` | sidebar, contextual and user menus |
| `league/flysystem-bundle` | media storage for uploads |
| `babdev/pagerfanta-bundle` | pagination of collection operations |
| `symfony/security-bundle` | permission checks on operations and properties |
| `symfony/ux-twig-component`, `symfony/ux-live-component`, `symfony/ux-autocomplete` | grid rendering and autocomplete form fields |

Optional, activated only when present:

| Package | Adds |
|---|---|
| `liip/imagine-bundle` | image thumbnails and filter sets |
| `symfony/workflow` | workflow-driven operations and link conditions |
| `symfony/object-mapper` | input/output mapping through `MappingProvider` and `MappingProcessor`. Must be a **non-dev** dependency: FrameworkBundle only registers its services then, and the bundle removes the mapping services when it finds none. |

## 2. Register the bundle

Symfony Flex does this for you. Otherwise, add it manually:

```php
// config/bundles.php
return [
    // ...
    Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
    League\FlysystemBundle\FlysystemBundle::class => ['all' => true],
    BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class => ['all' => true],
    LAG\AdminBundle\LAGAdminBundle::class => ['all' => true],
];
```

The bundle prepends configuration for `framework.cache`, `validation`, `twig`, `flysystem`,
`liip_imagine`, `babdev_pagerfanta` and `knp_menu`, so those extensions must be registered —
that is why `KnpMenuBundle`, `FlysystemBundle` and `BabDevPagerfantaBundle` are not optional.

## 3. Import the routing

The bundle registers a routing loader of type `lag_admin` that turns every operation into a
route. Import it once:

```php
// config/routes/lag_admin.php
declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routing): void {
    $routing->import('@LAGAdminBundle/config/routing.php');
};
```

YAML equivalent:

```yaml
# config/routes/lag_admin.yaml
lag_admin:
    resource: .
    type: lag_admin
```

Import it **once only** — the loader throws `Do not add the Admin routing loader "lag_admin" twice`
if it is imported a second time.

## 4. Configure the bundle

The minimum viable configuration declares where to look for metadata and at least one
application:

```php
// config/packages/lag_admin.php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('lag_admin', [
        'mapping' => [
            'paths' => [
                param('kernel.project_dir').'/src/Entity',
                param('kernel.project_dir').'/config/resources',
            ],
        ],
        'applications' => [
            'admin' => [
                'translation_domain' => 'admin',
                'translation_pattern' => '{resource}.{message}',
                'base_template' => 'admin/base.html.twig',
            ],
        ],
    ]);
};
```

`mapping.paths` defaults to `%kernel.project_dir%/src/Entity`. Add any directory holding
`#[Resource]` / `#[Grid]` attributes, or PHP configuration files returning a closure — see
[Resources](concepts/resources.md).

Every key is documented in [Configuration](configuration.md).

## 5. Install the assets

The bundle ships a compiled stylesheet and script under its `public/` directory, exposed as
`/bundles/lagadmin/`:

```bash
bin/console assets:install public
```

`@LAGAdmin/base.html.twig` loads Bootstrap 5 and Bootstrap Icons from a CDN, plus
`/bundles/lagadmin/admin.css` and `/bundles/lagadmin/admin.js`. If you provide your own base
template, you are responsible for loading whatever your templates need — see
[Templates](customization/templates.md).

## 6. Provide a base template (recommended)

`base_template` points at the layout every admin page extends. Start from the bundle's own:

```twig
{# templates/admin/base.html.twig #}
{% extends '@LAGAdmin/base.html.twig' %}

{% block page_title %}My back office{% endblock %}

{% block stylesheets %}
    {{ encore_entry_link_tags('admin') }}
{% endblock %}
```

## 7. Secure the back office

The bundle checks permissions through the `resource_access` voter, but it does not configure a
firewall for you. Add one:

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: login
                check_path: login
            logout:
                path: logout

    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
```

A ready-made login controller and form type are available if you want them:

```php
// config/routes.php
use LAG\AdminBundle\Controller\Security\Login;

$routing->add('login', '/login')->controller(Login::class);
$routing->add('logout', '/logout');
```

Then declare permissions per resource or per operation — see [Security](concepts/security.md).

## 8. Clear the cache

Routes and metadata are compiled into the container:

```bash
bin/console cache:clear
bin/console debug:router
```

## Building the bundle assets from source

Only needed when you hack on the bundle itself. Sources live in `assets/`, built with Webpack
Encore through Docker:

```bash
make assets          # yarn install + production build
make assets.watch    # development watch mode
```

The build output lands in the bundle's `public/` directory (`admin.css`, `admin.js`,
`favicon.ico`). Never edit those files by hand.
