# Reference — Resource options

`LAG\AdminBundle\Metadata\Attribute\Resource`, target: class, repeatable.

Most options are defaults inherited by the operations below the resource; an operation that sets
the same option wins.

| Option | Type | Default | Description |
|---|---|---|---|
| `shortName` | `?string` | the snake-cased class short name | Resource name. Must match `/^[a-z][a-z0-9_]*$/`. Derived from the class when the attribute sits on it; overridden by the name given to `LAGAdminBuilder::addResource()`. |
| `application` | `string` | `admin` | Application the resource belongs to. Must be declared in `lag_admin.applications`. |
| `resourceClass` | `?string` | `null` | Administered class. Filled automatically when the attribute sits on that class. Required. |
| `title` | `?string` | `null` | Human title. Metadata only — not used by the built-in menus. |
| `group` | `?string` | `null` | Groups the resource under a sidebar sub-menu labelled `lag_admin.menu.group.{group}`. |
| `icon` | `?string` | `null` | Icon name. Metadata only. |
| `pathPrefix` | `?string` | `null` | Replaces the pluralized resource segment in generated paths, and is prepended to explicit operation paths. |
| `permissions` | `?array<string>` | `null` | Roles allowed to access the operations. At least one is required. Empty means everybody. |
| `operations` | `array<OperationInterface>` | `Index`, `Show`, `Create`, `Update`, `Delete` | Operations. Passing a list replaces the default set entirely. At least one required. |
| `properties` | `array<PropertyInterface>` | `[]` | Properties, merged over those declared as attributes or introspected. |
| `provider` | `string` | `ORMProvider::class` | Default provider for operations. |
| `processor` | `?string` | `ORMProcessor::class` | Default processor for operations. |
| `identifiers` | `?array<string>` | `['id']` | Identifier fields. Drive paths, route parameters, the ORM `WHERE` clause and the fields excluded from generated forms. Introspected from Doctrine when omitted. |
| `routePattern` | `?string` | `{application}.{resource}.{operation}` | How route names are built. |
| `translationPattern` | `?string` | `null` | How translation keys are built. Falls back to the application pattern. |
| `translationDomain` | `?string` | `null` | Translation domain. Falls back to the application domain. |
| `form` | `?string` | `null` | Form type used by `Create` and `Update`. |
| `formOptions` | `?array` | `null` | Options passed to that form type. |
| `formTemplate` | `?string` | `null` | Template rendering the form body. |
| `validation` | `bool` | `true` | Whether submitted data is validated. |
| `validationContext` | `?array` | `null` | Validation context — e.g. `['groups' => ['admin']]`. |
| `ajax` | `bool` | `true` | Whether AJAX submissions are handled specially. |
| `normalizationContext` | `?array` | `null` | Serializer context used when normalizing. |
| `denormalizationContext` | `?array` | `null` | Serializer context used when denormalizing. |
| `input` | `?string` | `null` | DTO class the submitted data is mapped from (needs `symfony/object-mapper`). |
| `output` | `?string` | `null` | DTO class the provided data is mapped to. |
| `context` | `array` | `[]` | Arbitrary context merged into every operation context. |

## Reading the metadata

`ResourceInterface` / `ResourceMetadataInterface` expose a getter per option, plus:

| Method | Returns |
|---|---|
| `getName()` | `{application}.{shortName}` |
| `getOperation(string $name)` | one operation, by short or full name |
| `hasOperation(string $name)` | whether it exists |
| `getCollectionOperations()` | the collection operations only |
| `getProperty(string $name)` / `hasProperty()` | one property |
| `getPropertiesByType(string $class)` | every property of a given class |

Every `with*()` method returns a clone — metadata objects are immutable.
