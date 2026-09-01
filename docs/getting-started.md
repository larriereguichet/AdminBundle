# Getting started

This page takes you from an empty Symfony application to a working administration screen.
It assumes you already have Doctrine ORM entities. If you need the full picture — every
installation step, every configuration key — read [Installation](installation.md) and
[Configuration](configuration.md) instead.

## 1. Install the bundle

```bash
composer require lag/adminbundle
```

Without Symfony Flex, register the bundle yourself in `config/bundles.php`:

```php
return [
    // ...
    LAG\AdminBundle\LAGAdminBundle::class => ['all' => true],
];
```

## 2. Import the routes

The bundle generates one route per operation, at container compile time. Import its routing
loader once:

```php
// config/routes/lag_admin.php
declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routing): void {
    $routing->import('@LAGAdminBundle/config/routing.php');
};
```

Or in YAML:

```yaml
# config/routes/lag_admin.yaml
lag_admin:
    resource: .
    type: lag_admin
```

> Routes are built from your metadata when the container is compiled. After adding or renaming
> a resource or an operation, clear the cache (`bin/console cache:clear`).

## 3. Declare a resource

Add a `#[Resource]` attribute on the entity you want to administer:

```php
// src/Entity/Article.php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use LAG\AdminBundle\Metadata\Attribute as LAG;

#[LAG\Resource(shortName: 'article')]
#[LAG\Grid(name: 'articles', properties: ['title', 'publishedAt'])]
#[ORM\Entity]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
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

Three things happen from those two attributes:

* `#[Resource]` declares the resource `admin.article` with the five default operations —
  `index`, `show`, `create`, `update`, `delete`.
* `#[Text]` and `#[Date]` declare how each field is rendered in a grid and guessed in forms.
* `#[Grid]` declares a grid named `articles` listing two columns.

By default the bundle scans `%kernel.project_dir%/src/Entity` for these attributes.

## 4. Point the index operation at the grid

A collection operation only renders a grid when you name one:

```php
#[LAG\Resource(
    shortName: 'article',
    operations: [
        new LAG\Index(grid: 'articles'),
        new LAG\Show(),
        new LAG\Create(),
        new LAG\Update(),
        new LAG\Delete(),
    ],
)]
```

## 5. Configure the application

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

An *application* is a namespace for resources: routes, translations and the base template are
resolved per application. Resources default to the application named `admin`.

## 6. Browse it

Clear the cache and list the generated routes:

```bash
bin/console cache:clear
bin/console debug:router | grep article
```

You get:

| Route name              | Path                       |
|-------------------------|----------------------------|
| `admin.article.index`   | `/articles/index`          |
| `admin.article.show`    | `/articles/{id}/show`      |
| `admin.article.create`  | `/articles/create`         |
| `admin.article.update`  | `/articles/{id}/update`    |
| `admin.article.delete`  | `/articles/{id}/delete`    |

Open `/articles/index`.

> Prefer nicer URLs? Set `pathPrefix` on the resource and `path` on each operation — see
> [Routing](concepts/routing.md).

## What you get for free

* A paginated, sortable listing with create / update / delete links.
* Generated forms for create and update, guessed from your property metadata and your Doctrine
  mapping.
* A delete confirmation form.
* Flash messages, validation, redirects after submit.
* A sidebar menu listing every resource of the application.

## Where to go next

* Your listing needs search fields → [Filtering, sorting, pagination](concepts/filters.md).
* The generated form is not the one you want → [Forms](concepts/forms.md).
* The data does not come from a plain repository → [Providers and processors](concepts/providers-and-processors.md).
* The markup is not yours → [Templates](customization/templates.md).
* Not everybody may see everything → [Security](concepts/security.md).
