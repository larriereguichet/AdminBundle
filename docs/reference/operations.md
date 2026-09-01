# Reference — Operation options

Concrete operations live in `LAG\AdminBundle\Metadata\Attribute`: `Index`, `Show`, `Create`,
`Update`, `Delete`. `Index` extends `CollectionOperation`; the others extend `Operation`.

## Options shared by every operation

| Option | Type | Default | Description |
|---|---|---|---|
| `name` | `string` | the operation kind (`index`, `show`, …) | Operation short name. Must match `/^[a-z][a-z0-9_]*$/`. Give a distinct one to declare several operations of the same class. |
| `context` | `array` | `[]` | Arbitrary data handed to the provider and processor. Merged over the resource context. |
| `title` | `string\|false\|null` | generated | Page title. `false` renders none. Max 255 characters. |
| `description` | `?string` | `null` | Page description. |
| `icon` | `?string` | `null` | Icon name. |
| `template` | `?string` | per operation | Twig template of the page. |
| `baseTemplate` | `?string` | the application's | Layout the template extends. Use `@LAGAdmin/partial.html.twig` for no layout. |
| `permissions` | `?array<string>` | the resource's | Roles allowed. At least one is required; empty means everybody. |
| `controller` | `?string` | per operation | Controller service. |
| `route` | `?string` | generated | Route name. |
| `routeParameters` | `?array` | derived from identifiers | Map of route placeholder → property path. `[]` for a route with no parameter. |
| `methods` | `array<string>` | per operation | Allowed HTTP methods. `[]` means unrestricted. |
| `path` | `?string` | generated | Route path, prefixed by the resource `pathPrefix`. |
| `redirectRoute` | `?string` | the index route | Route redirected to after a successful write. |
| `redirectRouteParameters` | `?array` | `[]` | Parameters for that route. |
| `redirectOperation` | `?string` | `null` | Operation redirected to after a successful write. A name without a dot is resolved against the current resource. |
| `form` | `?string` | see [Forms](../concepts/forms.md) | Form type. |
| `formOptions` | `?array` | the resource's | Options passed to the form type. |
| `formTemplate` | `?string` | the resource's | Template rendering the form body. |
| `provider` | `string` | `ORMProvider::class` | Provider class name. |
| `processor` | `string` | `ORMProcessor::class` | Processor class name. |
| `identifiers` | `?array<string>` | the resource's (`['id']`) | Identifier fields for this operation, used to build the path segments and the route parameters. `[]` for an operation that addresses no particular record. Ignored by `Create`. |
| `contextualLinks` | `?array<Link\|string>` | `create` on `Index` | Links rendered in the contextual menu. |
| `itemLinks` | `?array<Link\|string>` | `update`, `delete` on `Index` | Links rendered per grid row. |
| `validation` | `?bool` | the resource's | Validate submitted data. |
| `validationContext` | `?array` | the resource's | Validation context. |
| `ajax` | `?bool` | the resource's | Handle AJAX submissions specially. |
| `normalizationContext` | `?array` | the resource's | Serializer context when normalizing. |
| `denormalizationContext` | `?array` | the resource's | Serializer context when denormalizing. |
| `input` | `?string` | `null` | DTO class the submitted data is mapped from. |
| `output` | `?string` | `null` | DTO class the provided data is mapped to. |
| `normalizationInput` | `?string` | `null` | Class used by the normalization decorators on input. |
| `normalizationOutput` | `?string` | `null` | Class used by the normalization decorators on output. |
| `workflow` | `?string` | `null` | Workflow name. |
| `workflowTransition` | `?string` | `null` | Transition applied before processing. |
| `embedded` | `bool` | `false` | Render without a layout, for inclusion in another page. |
| `flashMessage` | `?string` | per operation | Success message key. `null` stays silent. Exposed as `successMessage` on `Operation` — and `Show` names its own argument `successMessage` too. |

## Extra options on collection operations

`Index`, and any class extending `CollectionOperation`:

| Option | Type | Default | Description |
|---|---|---|---|
| `grid` | `?string` | `null` | Grid name. **Without it, no grid is rendered.** |
| `gridOptions` | `array` | `[]` | Options handed to the grid. |
| `pagination` | `bool` | `true` | Wrap the query in a pager. |
| `itemsPerPage` | `int` | `25` | Page size. Must be greater than 0. |
| `pageParameter` | `string` | `page` | Query parameter carrying the page. |
| `limit` | `?int` | `null` | Hard cap on the number of records, applied when pagination is off. Must be positive. |
| `filters` | `array<FilterInterface>` | `[]` | Filters of the listing. |
| `filterForm` | `?string` | `FilterType::class` | Form type of the filter form. |
| `filterFormOptions` | `array` | `['filters' => …]` | Options passed to it. |
| `batchOperations` | `array<string>` | `[]` | Operation names applicable to a selection of rows. |
| `collectionLinks` | `?array<Link>` | `[]` | Links rendered above the grid. |

## Defaults per operation class

| | `Index` | `Show` | `Create` | `Update` | `Delete` |
|---|---|---|---|---|---|
| `name` | `index` | `show` | `create` | `update` | `delete` |
| `controller` | `IndexResources` | `ShowResource` | `ProcessResource` | `ProcessResource` | `ProcessResource` |
| `provider` | `ORMProvider` | `ORMProvider` | `CreateProvider` | `ORMProvider` | `ORMProvider` |
| `processor` | `ORMProcessor` | `ORMProcessor` | `ORMProcessor` | `ORMProcessor` | `ORMProcessor` |
| `template` | `@LAGAdmin/resources/index.html.twig` | `…/show.html.twig` | `…/create.html.twig` | `…/update.html.twig` | `…/delete.html.twig` |
| `methods` | *(unrestricted)* | `GET` | `GET`, `POST` | `GET`, `POST`, `PUT` | `GET`, `POST` |
| `form` | `null` | — | `null` | `null` | `DeleteType` |
| `flashMessage` | `null` | `null` *(named `successMessage`)* | `lag_admin.ui.create_success` | `lag_admin.ui.process_success` | `lag_admin.ui.delete_success` |

## Link options

`LAG\AdminBundle\Metadata\Attribute\Link` extends `Property`, so it accepts every property
option (`name`, `label`, `attributes`, `condition`, `permissions`, `template`, `component`, …)
plus:

| Option | Type | Default | Description |
|---|---|---|---|
| `operation` | `?string` | `null` | Target operation. A name without a dot is resolved against the current resource. |
| `route` | `?string` | `null` | Target Symfony route. |
| `routeParameters` | `array` | `[]` | Parameters for that route. |
| `url` | `?string` | `null` | Raw URL. |
| `text` | `?string` | the link name | Link text. |
| `textPath` | `?string` | `null` | Property path read on the record to build the text. |
| `icon` | `?string` | `null` | Icon name, e.g. `bi:pencil`. |
| `type` | `?string` | `null` | Free-form marker available to templates. |
| `workflow` | `?string` | `null` | Workflow injected into the `condition` expression. |
| `workflowTransition` | `?string` | `null` | Transition exposed to the expression. |
| `component` | `?string` | `lag_admin:link` | Twig component rendering the link. |

A bare string in `contextualLinks` / `itemLinks` is shorthand for `new Link(operation: $string)`.
