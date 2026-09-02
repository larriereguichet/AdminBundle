# Templates

Every piece of markup the bundle renders lives in an overridable template. Nothing is inlined in
PHP.

## Three ways to replace a template

### 1. Point the metadata at your own template

The narrowest change, and the one to prefer:

```php
new Index(template: 'admin/articles/index.html.twig')
new Property(name: 'price', propertyPath: true, template: 'admin/articles/grid/price.html.twig')
new Grid(template: 'admin/articles/grids/table.html.twig')
new Update(formTemplate: 'admin/articles/form.html.twig')
```

Your template usually extends the bundle's and overrides one block:

```twig
{# admin/articles/index.html.twig #}
{% extends '@LAGAdmin/resources/index.html.twig' %}

{% block bottom_extra_content %}
    <p class="text-muted">{{ 'article.index_help'|trans({}, 'admin') }}</p>
{% endblock %}
```

### 2. Override a bundle template globally

Symfony's bundle template overriding applies: put a file with the same path under
`templates/bundles/LAGAdminBundle/` and it replaces the bundle's for the whole application.

```
templates/bundles/LAGAdminBundle/grids/properties/boolean.html.twig
templates/bundles/LAGAdminBundle/components/table_grid.html.twig
templates/bundles/LAGAdminBundle/menu/resource.html.twig
```

Use this for something that must change everywhere — the way booleans are rendered, the sidebar
markup. Prefer option 1 for a one-off.

### 3. Override a block in your base template

Layout-level changes belong in the application base template:

```twig
{# templates/admin/base.html.twig #}
{% extends '@LAGAdmin/base.html.twig' %}

{% block page_title %}My back office{% endblock %}

{% block admin_stylesheets %}
    {{ encore_entry_link_tags('admin') }}
{% endblock %}

{% block sidebar %}
    {% include 'admin/_sidebar.html.twig' %}
{% endblock %}
```

Then point the application at it:

```php
'applications' => [
    'admin' => ['base_template' => 'admin/base.html.twig'],
],
```

## Template map

### Layout

| Template | Purpose |
|---|---|
| `@LAGAdmin/base.html.twig` | full HTML layout; blocks `meta`, `favicon`, `page_title`, `admin_stylesheets`, `stylesheets`, `admin_javascripts`, `javascripts`, `header`, `flash_messages`, `container`, `layout`, `sidebar`, `title`, `content`, `footer`, `modal`, `loader` |
| `@LAGAdmin/partial.html.twig` | empty base, used by `embedded` operations |
| `@LAGAdmin/layout/header.html.twig` | header with the contextual and user menus |
| `@LAGAdmin/layout/sidebar.html.twig` | sidebar with the resource menu |
| `@LAGAdmin/session/flash_messages.html.twig` | flash messages |

### Operations

| Template | Used by |
|---|---|
| `@LAGAdmin/resources/operation.html.twig` | base for all operation templates; blocks `title`, `content`, `form`, `form_content`, `form_buttons`, `cancel`, `submit` |
| `@LAGAdmin/resources/index.html.twig` | `Index`; blocks `collection_links`, `filters`, `grid`, `pagination`, `bottom_extra_content` |
| `@LAGAdmin/resources/show.html.twig` | `Show` |
| `@LAGAdmin/resources/create.html.twig` | `Create` |
| `@LAGAdmin/resources/update.html.twig` | `Update` |
| `@LAGAdmin/resources/delete.html.twig` | `Delete` |

### Grids and cells

| Template | Purpose |
|---|---|
| `@LAGAdmin/components/grid.html.twig` | grid component; blocks `title`, `header`, `rows`, `empty`, `footer` |
| `@LAGAdmin/components/table_grid.html.twig` | `table` grid type; blocks `header`, `body` |
| `@LAGAdmin/components/card_grid.html.twig` | `card` grid type |
| `@LAGAdmin/components/row.html.twig` | one row |
| `@LAGAdmin/components/cell.html.twig` | one cell |
| `@LAGAdmin/components/table_header.html.twig` | one header cell |
| `@LAGAdmin/components/grid_title.html.twig` | the grid title, included from the `title` block |
| `@LAGAdmin/components/links.html.twig` | a group of links |
| `@LAGAdmin/grids/properties/*.html.twig` | one per property type: `text`, `boolean`, `date`, `image`, `link`, `map`, `count`, `collection`, `group`, `rich_text`, `slug`, `title`, `form`, `action`, `resource_link` |

### Forms, menus, security

| Template | Purpose |
|---|---|
| `@LAGAdmin/forms/theme.html.twig` | global form theme, registered automatically |
| `@LAGAdmin/forms/form.html.twig` | default form body |
| `@LAGAdmin/menu/menu-base.html.twig` | KnpMenu base template |
| `@LAGAdmin/menu/resource.html.twig` | sidebar menu |
| `@LAGAdmin/menu/horizontal.html.twig` | contextual menu |
| `@LAGAdmin/menu/user.html.twig` | user dropdown |
| `@LAGAdmin/security/login.html.twig` | login page |

## Variables available in a template

**Operation templates** receive:

| Variable | Contents |
|---|---|
| `resource` | the resource metadata |
| `operation` | the operation metadata |
| `data` | whatever the provider returned |
| `{resourceName}` | the same data, under the camel-cased resource name — `article`, or `articles` for a collection operation |
| `grid` | the built `GridView`, on collection operations with a grid |
| `form`, `filterForm`, `batchForm` | form views, when applicable |
| `baseTemplate` | the layout to extend |

**Property templates** receive:

| Variable | Contents |
|---|---|
| `data` | the value resolved from the property path |
| `property` | the property metadata, with all its options |
| `attributes` | the cell HTML attributes, rendered with `{{ attributes }}` |
| `cell` | the `CellView` |
| `context` | the grid context |

```twig
{# admin/articles/grid/price.html.twig #}
<p {{ attributes }}>
    {{ (data.price / 100)|format_currency('EUR') }}
    {% if property.suffix %}{{ property.suffix }}{% endif %}
</p>
```

## The `lag_admin` Twig global

Available everywhere:

```twig
{{ lag_admin.resource.shortName }}
{{ lag_admin.operation.title }}
```

Both are `null` outside an admin request.

## Twig functions

| Function | Purpose |
|---|---|
| `lag_admin_path(operationName, data)` | relative URL of an operation |
| `lag_admin_url(operationName, data)` | absolute URL of an operation |
| `lag_admin_link(link, data)` | URL of a `Link` metadata object |
| `lag_admin_sort_url(header)` | URL toggling the sort on a column |
| `lag_admin_operation_allowed(operationName)` | permission check |
| `lag_admin_is_pager(data)` | whether the data is a Pagerfanta pager |
| `lag_attributes(array)` | render an attribute array as HTML attributes |
| `\|lag_admin_rich_text` | render a rich-text (Quill Delta) value |

## Assets

`@LAGAdmin/base.html.twig` loads Bootstrap 5 and Bootstrap Icons from a CDN, plus
`/bundles/lagadmin/admin.css` and `/bundles/lagadmin/admin.js` (installed by
`bin/console assets:install`). Replace either through the `admin_stylesheets` and
`admin_javascripts` blocks.

The bundle's JavaScript registers Stimulus controllers for collections, collapsibles, modals,
rich-text areas and select widgets. If you drop `admin.js`, those stop working.

## Next

[Twig components](twig-components.md).
