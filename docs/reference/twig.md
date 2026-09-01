# Reference — Twig

## Global

`lag_admin` is available in every template:

| Property | Returns |
|---|---|
| `lag_admin.resource` | the current `ResourceInterface`, or `null` |
| `lag_admin.operation` | the current `OperationInterface`, or `null` |

```twig
{% if lag_admin.operation %}
    <body class="admin admin-{{ lag_admin.resource.shortName }}">
{% endif %}
```

## Functions

| Function | Signature | Returns |
|---|---|---|
| `lag_admin_path` | `(string $operationName, mixed $data = null)` | relative URL of an operation, route parameters resolved from `$data` |
| `lag_admin_url` | `(string $operationName, mixed $data = null)` | the same, absolute |
| `lag_admin_link` | `(Link $link, mixed $data = null)` | URL of a `Link`, handling its `url`, `route` or `operation` |
| `lag_admin_sort_url` | `(HeaderView $header)` | URL toggling the sort of a column |
| `lag_admin_operation_allowed` | `(string $operationName)` | whether the current user may access the operation |
| `lag_admin_is_pager` | `(mixed $data)` | whether the data is a Pagerfanta pager |
| `lag_attributes` | `(array $attributes)` | renders an attribute array as HTML attributes |

```twig
<a href="{{ lag_admin_path('admin.article.update', article) }}">Edit</a>

{% if lag_admin_operation_allowed('admin.article.delete') %}
    <a href="{{ lag_admin_path('admin.article.delete', article) }}">Delete</a>
{% endif %}
```

## Filters

| Filter | Purpose |
|---|---|
| `lag_admin_rich_text` | renders a rich-text (Quill Delta) value as HTML |

## Components

| Component | Props |
|---|---|
| `lag_admin:grid` | `grid` (`GridView`), `data` |
| `lag_admin:table_grid` | `grid` (`GridView`), `data` — the default component of a grid |
| `lag_admin:row` | `row` (`RowView`) |
| `lag_admin:cell` | `cell` (`CellView`), `data` |
| `lag_admin:table_header` | `header` (`HeaderView`) |
| `lag_admin:links` | `links` (list of `Link`) |
| `lag_admin:link` | mounted from a `CellView` |
| `lag_admin:text` | mounted from a `CellView` |
| `lag_admin:map` | mounted from a `CellView` |
| `lag_admin:image` | mounted from a `CellView` |
| `lag_admin:form` | mounted from a `CellView` |

```twig
<twig:lag_admin:grid grid="{{ grid }}" data="{{ data }}" />
```

## Variables in operation templates

| Variable | Contents |
|---|---|
| `resource` | resource metadata |
| `operation` | operation metadata |
| `data` | whatever the provider returned |
| `{resourceName}` | the same data, under the camel-cased resource name (pluralized for collection operations) |
| `grid` | the `GridView`, on collection operations with a grid |
| `form`, `filterForm`, `batchForm` | form views, when applicable |
| `baseTemplate` | layout to extend |
| `responseCode` | optional HTTP status override |

## Variables in property templates

| Variable | Contents |
|---|---|
| `data` | value resolved from the property path, after the data transformer |
| `property` | the property metadata, with all its options |
| `attributes` | cell HTML attributes — render with `{{ attributes }}` |
| `cell` | the `CellView` |
| `context` | the grid context |
| `component` | the component name, when the property uses one |
