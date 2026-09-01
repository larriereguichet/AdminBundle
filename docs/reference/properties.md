# Reference — Property options

All property classes live in `LAG\AdminBundle\Metadata\Attribute` and extend `Property`. They
target class properties, and are repeatable.

## Options shared by every property

| Option | Type | Default | Description |
|---|---|---|---|
| `name` | `?string` | the field name | Property name. 1 to 255 characters. |
| `propertyPath` | `string\|bool\|null` | the name | PropertyAccess path. `true` or `'.'` passes the whole record; `false` passes nothing. |
| `label` | `string\|bool\|null` | from the translation pattern | Column header. `false` renders none. |
| `template` | `?string` | per class | Twig template rendering the cell. Must be a valid template. |
| `component` | `?string` | per class | Twig component rendering the cell. |
| `sortable` | `bool` | per class | Whether the header is a sort link. |
| `sortingPath` | `?string` | the property path | Field actually sorted on. Required when `sortable` is true. |
| `translatable` | `bool` | per class | Whether the rendered value goes through the translator. |
| `translationDomain` | `?string` | the resource's | Domain used for the label and the value. |
| `attributes` | `array` | `[]` | HTML attributes of the cell. |
| `rowAttributes` | `array` | `[]` | HTML attributes of the row. |
| `headerAttributes` | `array` | `[]` | HTML attributes of the header cell. |
| `dataTransformer` | `?string` | per class | `DataTransformerInterface` applied to the value before rendering. |
| `permissions` | `?array<string>` | `null` | Roles required to see the cell. |
| `condition` | `?string` | `null` | ExpressionLanguage expression deciding whether the cell renders. |

## Per-class defaults and extra options

### `Property`

The generic property. Template: none — set one. `sortable: true`, `translatable: false`.

### `Text`

Template `@LAGAdmin/grids/properties/text.html.twig`, `sortable: true`, `translatable: true`.

| Option | Type | Default |
|---|---|---|
| `length` | `int` | `100` |
| `replace` | `string` | `...` |
| `empty` | `string` | `~` |
| `prefix` | `string` | `''` |
| `suffix` | `string` | `''` |

### `Title`

Same options as `Text`. Template `@LAGAdmin/grids/properties/title.html.twig`,
`sortable: false`.

### `RichText`

Same options as `Text`. Template `@LAGAdmin/grids/properties/rich_text.html.twig`,
`sortable: false`. The value is rendered as HTML.

### `Boolean`

Template `@LAGAdmin/grids/properties/boolean.html.twig`. No extra option.

### `Date`

Template `@LAGAdmin/grids/properties/date.html.twig`.

| Option | Type | Default | Values |
|---|---|---|---|
| `dateFormat` | `string` | `medium` | `none`, `short`, `medium`, `long`, `full`, or an ICU pattern |
| `timeFormat` | `string` | `none` | same |

### `Map`

Template `@LAGAdmin/grids/properties/map.html.twig`.

| Option | Type | Default | Description |
|---|---|---|---|
| `map` | `array` | `[]` | Value → label. At least one entry. |

Pair with `dataTransformer: EnumDataTransformer::class` for backed enums. In forms, guessed as a
`ChoiceType` built from the map.

### `Count`

Template `@LAGAdmin/grids/properties/count.html.twig`, `dataTransformer:
CountDataTransformer::class`. Renders the size of a collection.

### `Collection`

Template `@LAGAdmin/grids/properties/collection.html.twig`, `sortable: false`.

| Option | Type | Default | Description |
|---|---|---|---|
| `entryProperty` | `?PropertyInterface` | `null` | Property rendering each entry. Required. |

### `Compound`

Template `@LAGAdmin/grids/properties/collection.html.twig`, `sortable: false`.

| Option | Type | Default | Description |
|---|---|---|---|
| `properties` | `array<PropertyInterface>` | `[]` | Properties rendered inside the cell. |

### `Group`

Template `@LAGAdmin/grids/properties/group.html.twig`, `propertyPath: true`,
`translatable: false`.

| Option | Type | Default | Description |
|---|---|---|---|
| `properties` | `array<string>` | `[]` | Names of other resource properties rendered inside the cell. |

### `Image`

Template `@LAGAdmin/grids/properties/image.html.twig`, `sortable: false`,
`dataTransformer: ImageDataTransformer::class`.

| Option | Type | Default | Description |
|---|---|---|---|
| `imageFilter` | `?string` | `null` | LiipImagine filter set. |
| `storage` | `?string` | `null` | Flysystem storage holding the file. |
| `upload` | `bool` | `true` | Whether the property accepts uploads in forms. |

### `Link`

`component: lag_admin:link`, `sortable: false`. See
[Operation reference — Link options](operations.md#link-options).

### `ResourceLink`

Extends `Text`. Links to an operation of another resource.

| Option | Type | Description |
|---|---|---|
| `application` | `?string` | Target application. Required. |
| `resource` | `?string` | Target resource. Required. |
| `operation` | `?string` | Target operation. Required. |

### `Slug`

Template `@LAGAdmin/grids/properties/slug.html.twig`, `sortable: false`.

| Option | Type | Default | Description |
|---|---|---|---|
| `source` | `string\|array` | `name` | Property (or properties) the slug is generated from. |
| `slugger` | `string` | `default` | Slugger used. |

Declaring a `Slug` property makes `GenerateSlugListener` fill it on every write.

### `Form`

`dataTransformer: FormDataTransformer::class`, `translatable: false`.

| Option | Type | Default | Description |
|---|---|---|---|
| `form` | `string` | `FormType::class` | Form type rendered in the cell. |
| `formOptions` | `array` | `[]` | Options passed to it. |
| `formTemplate` | `?string` | `null` | Template rendering the form. |
| `properties` | `array` | `[]` | Properties rendered alongside the form. |

## Expression context of `condition`

| Variable | Contents |
|---|---|
| `this`, `data` | the value being rendered |
| `object` | the object being rendered |
| `resource` | the row data |
| `auth_checker` | the authorization checker |
| `workflow` | the workflow, when the property declares one |
| `workflow_transition` | the transition, when declared |

`is_granted()` is available, as in any Symfony security expression.
