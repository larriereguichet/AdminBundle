# Menus

Menus are built with KnpMenu. The bundle registers three builders; the default layout renders
all three.

| Menu alias | Builder | Rendered in | Contents |
|---|---|---|---|
| `resource` | `ResourceMenuBuilder` | the sidebar | one entry per resource |
| `contextual` | `ContextualMenuBuilder` | the header | the current operation's contextual links |
| `user` | `UserMenuBuilder` | the header | the user dropdown |

```twig
{{ knp_menu_render('resource', {template: '@LAGAdmin/menu/resource.html.twig'}) }}
{{ knp_menu_render('contextual', {template: '@LAGAdmin/menu/horizontal.html.twig'}) }}
{{ knp_menu_render('user', {template: '@LAGAdmin/menu/user.html.twig'}) }}
```

## The resource menu

Every resource with **at least one collection operation** gets an entry, pointing at its last
collection operation. A resource with only item operations — a "my account" screen, for example
— never appears.

The entry label is the translation key `lag_admin.menu.{pluralized_short_name}`:

```yaml
# translations/admin.en.yaml
lag_admin:
    menu:
        articles: Articles
        orders: Orders
```

### Grouping

Resources sharing a `group` are nested under a sub-menu labelled
`lag_admin.menu.group.{group}`:

```php
new Resource(shortName: 'article', group: 'content')
new Resource(shortName: 'page', group: 'content')
```

```yaml
lag_admin:
    menu:
        group:
            content: Content
```

Ungrouped resources are listed after the groups.

## The contextual menu

Fed by the current operation's `contextualLinks`. An `Index` gets a *create* link by default;
declaring `contextualLinks` replaces the default list.

```php
new Index(
    contextualLinks: [
        new Link(operation: 'create', text: 'article.create', icon: 'bi:circle-plus'),
        new Link(operation: 'export', text: 'article.export', icon: 'bi:download'),
    ],
)
```

Each link resolves to a URL through its `url`, its `route`, or its `operation`. Icons land in
the KnpMenu item extras under `icon`, and `create`, `update` and `delete` get default icons when
none is given.

## Customising the menus

Three levels, from cheapest to most involved:

**Override the template.** Copy `@LAGAdmin/menu/resource.html.twig` (or `horizontal`, `user`,
`menu-base`) into `templates/bundles/LAGAdminBundle/menu/` and adjust the markup. See
[Templates](../customization/templates.md).

**Override the layout block.** Sidebar and header are included from
`@LAGAdmin/base.html.twig` through the `sidebar` and `header` blocks — override them in your own
base template:

```twig
{% extends '@LAGAdmin/base.html.twig' %}

{% block sidebar %}
    {{ knp_menu_render('app_admin', {template: 'admin/menu/sidebar.html.twig'}) }}
{% endblock %}
```

**Write your own builder.** A plain KnpMenu builder service tagged `knp_menu.menu_builder`:

```php
$services->set(App\Menu\AdminMenuBuilder::class)
    ->tag('knp_menu.menu_builder', ['method' => 'build', 'alias' => 'app_admin'])
;
```

Inject `lag_admin.resource.collection_factory` to iterate over the resources, and
`lag_admin.routing.route_name_generator` to build route names.

The bundle also registers a KnpMenu factory extension (`ResourceExtension`), so a menu item can
carry an operation name in its options and get its URL resolved for you.

## Next

[Events](events.md).
