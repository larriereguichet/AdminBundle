# Customization: which extension point?

The bundle is designed to be extended by **composition**: decorate a service, listen to an
event, override a template. Subclassing a bundle class or replacing a controller is almost never
the right answer.

Find your problem in the left column.

| I want to… | Do this |
|---|---|
| change the markup of a page | override the operation `template`, or the bundle template — [Templates](templates.md) |
| change the markup of one column | give the property a `template`, or write a Twig component — [Templates](templates.md), [Twig components](twig-components.md) |
| change the layout of every admin page | set `base_template` on the application |
| add a column that is not a field | `new Property(name: …, propertyPath: true, template: …)` — [Custom properties](custom-properties.md) |
| reuse a display style across resources | write a property class — [Custom properties](custom-properties.md) |
| format a value before rendering | a `DataTransformerInterface` — [Custom properties](custom-properties.md#data-transformers) |
| read data from somewhere else | a `ProviderInterface` — [Custom state](state.md) |
| do something on save | a `ProcessorInterface`, or a `data_process` listener — [Custom state](state.md) |
| add behaviour to *every* operation | decorate `ProviderInterface` / `ProcessorInterface` — [Custom state](state.md) |
| return a different response | listen to `ResourceControllerEvent` — [Events](../concepts/events.md) |
| add an action button | a `Link` in `contextualLinks` or `itemLinks` — [Operations](../concepts/operations.md#links) |
| add a screen that is not CRUD | a named `Show` or `Update` operation with its own template and provider |
| restrict who sees what | `permissions` and `condition` — [Security](../concepts/security.md) |
| drive an operation with a state machine | `workflow` and `workflowTransition` — [Workflow](workflow.md) |
| store uploaded files elsewhere | a Flysystem storage — [Uploads and images](uploads.md) |
| change the columns at runtime | a `GridProviderInterface` — [Grids](../concepts/grids.md#building-grids-dynamically) |
| add data to the operation context | a `ContextBuilderInterface` — [Providers and processors](../concepts/providers-and-processors.md#adding-entries-to-the-context) |

## The three rules

**Type-hint interfaces.** `ResourceInterface`, `OperationInterface`, `PropertyInterface`,
`ProviderInterface`, `ProcessorInterface`, `GridInterface`. Concrete classes are implementation
details.

**Metadata objects are immutable.** Never mutate one — build a variant:

```php
$operation = $operation->withTitle('article.custom_title');
```

Every `with*()` method clones. This is why a metadata factory is a decorator chain: each factory
takes the metadata built so far and returns a new object.

**Do not override controllers.** The three controllers orchestrate provider, form, processor,
events and response handler. Everything they do is delegated to a service you can replace.

## Custom operations without a custom controller

Ninety percent of "custom actions" are a named `Update` with a provider, a processor and a
template:

```php
new Update(
    name: 'archive',
    title: 'article.archive',
    path: '/{id}/archive',
    template: 'admin/articles/archive.html.twig',
    form: FormType::class,
    provider: ArticleProvider::class,
    processor: ArchiveArticleProcessor::class,
    redirectOperation: 'index',
    flashMessage: 'article.archived',
)
```

You get the route, the permission check, the form, the validation, the flash message and the
redirect for free.

If you really need your own controller, point the operation at it — it will receive the
resolved metadata as arguments:

```php
new Show(name: 'dashboard', path: '/dashboard', controller: DashboardController::class)
```

```php
final readonly class DashboardController
{
    public function __invoke(OperationInterface $operation, Request $request): Response
    {
        // …
    }
}
```

`ResourceMetadataInterface`, `OperationMetadataInterface` and `GridMetadataInterface` arguments
are injected by the bundle's value resolvers.
