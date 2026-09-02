[![Latest Stable Version](https://poser.pugx.org/lag/adminbundle/v/stable)](https://packagist.org/packages/lag/adminbundle)
[![Total Downloads](https://poser.pugx.org/lag/adminbundle/downloads)](https://packagist.org/packages/lag/adminbundle)
[![CI](https://github.com/larriereguichet/AdminBundle/actions/workflows/ci.yaml/badge.svg)](https://github.com/larriereguichet/AdminBundle/actions/workflows/ci.yaml)
[![License](https://poser.pugx.org/lag/adminbundle/license)](LICENSE)

# AdminBundle

The AdminBundle builds an administration interface from PHP attributes. Put a `#[Resource]` on
an entity, and the bundle generates the routes, the controllers, the grids, the forms and the
menus from that metadata — no code generation, nothing to maintain by hand.

Everything it generates is replaceable: a provider, a processor, a template, a Twig component
or an event listener is enough to bend a screen to what your application actually needs.

## Features

* Declarative configuration through PHP attributes, or PHP configuration files
* Doctrine ORM integration out of the box — provider, processor, identifiers, form guessing
* Listings with pagination, sorting, filtering and batch operations
* Generated forms, guessed from the property metadata and the Doctrine mapping
* Per-resource, per-operation and per-property permissions
* Dynamic menus, translations and route generation
* Rendering through Symfony UX Twig components, every template overridable
* Rich text through QuillJS, and optional integrations: LiipImagine, `symfony/workflow`,
  `symfony/object-mapper`

## Requirements

| Requirement | Version |
|---|---|
| PHP | 8.4 or higher |
| Symfony | 7.0 or higher |
| Doctrine ORM | required for the default provider and processor |

## Quick start

### 1. Install the bundle

```bash
composer require lag/adminbundle
```

Without Symfony Flex, register it yourself in `config/bundles.php` — see
[Installation](docs/installation.md).

### 2. Import the routing

```php
// config/routes/lag_admin.php
declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routing): void {
    $routing->import('@LAGAdminBundle/config/routing.php');
};
```

### 3. Declare a resource

```php
// src/Entity/Article.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use LAG\AdminBundle\Metadata\Attribute as LAG;

#[LAG\Resource(shortName: 'article', operations: [
    new LAG\Index(grid: 'articles'),
    new LAG\Show(),
    new LAG\Create(),
    new LAG\Update(),
    new LAG\Delete(),
])]
#[LAG\Grid(name: 'articles', properties: ['title', 'publishedAt'])]
#[ORM\Entity]
class Article
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[LAG\Text]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[LAG\Date]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    // getters and setters
}
```

The resource is named `admin.article`. Declaring `operations` replaces the defaults, so the five
are listed here to point `index` at the grid — `#[LAG\Resource(shortName: 'article')]` alone
already gives you the same five.

### 4. Configure the application

```php
// config/packages/lag_admin.php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('lag_admin', [
        'applications' => [
            'admin' => [
                'translation_domain' => 'admin',
                'base_template' => '@LAGAdmin/base.html.twig',
            ],
        ],
    ]);
};
```

### 5. Browse it

Routes are built when the container is compiled, so clear the cache after adding a resource:

```bash
bin/console cache:clear
bin/console debug:router | grep article
```

Open `/articles/index`.

The full walkthrough is in [Getting started](docs/getting-started.md).

## Documentation

The documentation lives in [`docs/`](docs/index.md).

* [Installation](docs/installation.md) — dependencies, routing, assets, security
* [Getting started](docs/getting-started.md) — one entity to a working screen
* [Configuration](docs/configuration.md) — every `lag_admin` key
* [Concepts](docs/index.md#concepts) — resources, operations, properties, grids, forms,
  filters, providers and processors, routing, security, menus, events, translations
* [Reference](docs/index.md#reference) — every option of every attribute
* [Customization](docs/index.md#customization) — templates, Twig components, custom
  properties, state, uploads, workflow

## Contributing

The quality suite must be green before a change is proposed:

```bash
make tests          # phpunit + phpstan + rector + var-dump-check + cs
make cs.fix         # apply the code style fixes
```

Functional tests need the database container: `docker compose up -d database`. The conventions
this repository follows — coding standards, commit format — are in [CLAUDE.md](CLAUDE.md) and
[`.claude/rules/`](.claude/rules/). How AI assistants are used here is stated in
[AI_USAGE.md](AI_USAGE.md).

## Changelog

Release notes and breaking changes are in [HISTORY.md](HISTORY.md).

## License

[MIT](LICENSE).
