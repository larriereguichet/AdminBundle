# Forms

`Create`, `Update` and `Delete` operations run a Symfony form. You can let the bundle build it,
or hand it your own form type.

## How the form is chosen

For each operation, in order:

1. the operation's `form` option;
2. the resource's `form` option (with its `formOptions`);
3. for `Create` and `Update`, the generated `ResourceDataType`;
4. for `Delete`, `DeleteType` — a confirmation form.

So the smallest possible resource already has working create and update screens.

## The generated form

`ResourceDataType` builds one field per property of the resource, skipping the identifiers. The
type and options of each field come from the **form guesser**:

| Property | Guessed form type |
|---|---|
| `Text`, `Slug`, `Title` | `TextType` |
| `RichText` | `TextareaType` (the bundle's, with a rich-text controller) |
| `Boolean` | `CheckboxType` |
| `Date` | `DateType` |
| `Map` | `ChoiceType`, choices built from the map |
| `Collection` | `CollectionType`, the entry type guessed from `entryProperty` |
| anything else | not guessed — the field is skipped |

With Doctrine installed, `MetadataFormGuesser` decorates the guesser and fills the gaps from the
ORM mapping (associations become entity fields, column types refine the guess).

Guessed fields are `required: false`, and carry the property's `property_path` when it differs
from the field name.

## Using your own form type

```php
new Resource(
    shortName: 'article',
    form: ArticleType::class,
    formOptions: ['validation_groups' => ['admin']],
    formTemplate: 'admin/articles/form.html.twig',
)
```

Per operation, when create and update differ:

```php
operations: [
    new Create(form: CreateArticleType::class),
    new Update(form: UpdateArticleType::class),
]
```

The form's `data_class` should be your resource class. The data handed to it comes from the
provider — a new instance for `Create`, the loaded record for `Update`.

## The form template

`formTemplate` replaces the body of the form, keeping the surrounding layout, buttons and
`form_start` / `form_end`:

```twig
{# admin/articles/form.html.twig #}
<div class="row">
    <div class="col-8">{{ form_row(form.title) }}</div>
    <div class="col-4">{{ form_row(form.publishedAt) }}</div>
</div>
{{ form_rest(form) }}
```

The default is `@LAGAdmin/forms/form.html.twig`, which renders the whole form in one go. The
bundle also registers `@LAGAdmin/forms/theme.html.twig` as a global form theme.

## Validation

Data is validated by the `ValidationProcessor` decorator before it reaches your processor, using
the constraints declared on your entity. Turn it off, or scope it:

```php
new Update(
    validation: true,
    validationContext: ['groups' => ['admin']],
)
```

`validation: false` skips the decorator entirely.

## Bundled form types

| Type | Purpose |
|---|---|
| `Image\ImageType` | image upload, with preview and removal |
| `Media\GalleryType` | pick an existing media from the gallery |
| `AutoComplete\AutoCompleteType` | Symfony UX autocomplete over a resource |
| `Text\TextareaType` | textarea wired to the rich-text controller |
| `Resource\ResourceDataType` | the generated resource form |
| `Resource\ResourceCollectionType` | collection of embedded resources |
| `Resource\ResourceDataChoiceType` | choice over the records of a resource |
| `Resource\DeleteType` | delete confirmation |
| `Resource\FilterType` | the generated filter form |
| `Resource\BatchType` | batch operation selector |
| `Security\LoginType` | login form used by the bundled login controller |

## Form type extensions

Two Symfony form types get extra options from the bundle, on every form of your application.

`ChoiceType` — and every type extending it, `EntityType` included:

| Option | Type | Default | Effect |
|---|---|---|---|
| `select2` | `bool` | `false` | Tags the widget with the `lag-admin-select2` Stimulus controller and moves `multiple` to a data attribute |
| `allow_add` | `bool` | `false` | Lets the user create a value that is not in the list |

```php
$builder->add('tags', EntityType::class, [
    'class' => Tag::class,
    'multiple' => true,
    'select2' => true,
    'allow_add' => true,
]);
```

`CollectionType`:

| Option | Type | Default | Effect |
|---|---|---|---|
| `add_label` | `string` | `lag_admin.ui.add` | Label of the "add an entry" button |
| `delete_label` | `string` | `lag_admin.ui.delete` | Label of the "remove an entry" button |

Both are translation keys, resolved in the form's translation domain.

## Forms inside a grid

A `Form` property embeds a form in a grid cell — see
[Properties](properties.md#form).

## Forms on a collection operation

An `Index` with a `form` option renders one form per row inside a `CollectionType`, submitting
the whole collection at once. This is how bulk-edit screens are built:

```php
new Index(
    name: 'prepare_all',
    path: '/prepare',
    form: FormType::class,
    formOptions: ['label' => false],
    provider: OrdersToPrepareProvider::class,
    processor: PrepareOrdersProcessor::class,
    pagination: false,
)
```

## Next

[Filtering, sorting, pagination](filters.md).
