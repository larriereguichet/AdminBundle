# Translations

Labels, titles, flash messages and menu entries are translation keys, not literals. The keys are
built from a **translation pattern**.

## The pattern

Set per application, and overridable per resource:

```php
'applications' => [
    'admin' => [
        'translation_domain' => 'admin',
        'translation_pattern' => '{application}.{resource}.{message}',
    ],
],
```

```php
new Resource(
    shortName: 'article',
    translationPattern: 'article.{message}',
    translationDomain: 'admin',
)
```

Placeholders: `{application}`, `{resource}`, `{message}`. `{message}` is the snake-cased
property or message name.

| Pattern | Property `publishedAt` | Key |
|---|---|---|
| `{application}.{resource}.{message}` | `publishedAt` | `admin.article.published_at` |
| `{resource}.{message}` | `publishedAt` | `article.published_at` |

Without a pattern, the label falls back to the humanized property name (`Published At`).

## Domains

`translation_domain` on the application is the default for every resource; a resource can
override it. Operation titles, grid titles and property labels are translated in that domain.

```yaml
# translations/admin.en.yaml
article:
    title: Title
    published_at: Published on
    articles: Articles
```

A property can use a different domain:

```php
new Text(name: 'title', translationDomain: 'content')
```

## Opting out

```php
new Text(name: 'sku', label: 'SKU', translatable: false)   // rendered as-is
new Text(name: 'code', label: false)                       // no header at all
new Index(title: false)                                    // no page title
```

`translatable` controls whether the *value* goes through the translator; `label` controls the
header.

## Bundle messages

The bundle's own messages live under the `lag_admin.*` namespace in the `admin` domain:

| Key | Used for |
|---|---|
| `lag_admin.ui.create`, `.update`, `.delete` | default link labels |
| `lag_admin.ui.save`, `.cancel` | form buttons |
| `lag_admin.ui.no_record` | empty grid |
| `lag_admin.ui.create_success`, `.process_success`, `.delete_success` | flash messages — translated in the `flashes` domain, not in the application one |
| `lag_admin.menu.{resources}` | sidebar entries |
| `lag_admin.menu.group.{group}` | sidebar groups |
| `lag_admin.batch.select_all` | batch checkbox |
| `lag_admin.delete.*` | delete confirmation screen |

> The catalogues shipped with the bundle are incomplete: `translations/admin.fr.yaml` is
> partially filled, `translations/admin.en.yaml` is empty, and `translations/flashes.{en,fr}.yaml`
> only define three keys. Until they are completed, define
> the keys you need in your own `translations/admin.{locale}.yaml` — application catalogues take
> precedence over bundle ones.

## Flash messages

Each write operation carries a message key:

```php
new Update(flashMessage: 'article.updated')
new Delete(flashMessage: null)   // stay silent
```

They are rendered by `@LAGAdmin/session/flash_messages.html.twig`, included from the base
template.

## Next

[Customization overview](../customization/overview.md).
