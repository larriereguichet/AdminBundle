# Providers and processors

Reading and writing are two separate, replaceable services.

* A **provider** returns the data an operation needs.
* A **processor** persists the data an operation produced.

Both are chosen per operation, both default to the Doctrine ORM implementations.

## The interfaces

```php
namespace LAG\AdminBundle\State\Provider;

interface ProviderInterface
{
    /**
     * @param array<string, mixed> $urlVariables identifiers extracted from the request path
     * @param array<string, mixed> $context      operation context, page, sort, filters…
     */
    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed;
}
```

```php
namespace LAG\AdminBundle\State\Processor;

interface ProcessorInterface
{
    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void;
}
```

## Defaults

| Operation | Provider | Processor |
|---|---|---|
| `Index` | `ORMProvider` | `ORMProcessor` |
| `Show` | `ORMProvider` | *(unused)* |
| `Create` | `CreateProvider` | `ORMProcessor` |
| `Update` | `ORMProvider` | `ORMProcessor` |
| `Delete` | `ORMProvider` | `ORMProcessor` |

`ORMProvider` builds a query builder on the resource class and adds a `WHERE` clause for each
identifier present in the URL. `CreateProvider` returns a new, empty instance of the resource
class. `ORMProcessor` persists, flushes or removes depending on the operation.

## Writing a provider

```php
namespace App\State\Provider;

use App\Repository\ArticleRepositoryInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

final readonly class PublishedArticlesProvider implements ProviderInterface
{
    public function __construct(
        private ArticleRepositoryInterface $repository,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        return $this->repository->createPublishedQueryBuilder();
    }
}
```

Point an operation at it by class name:

```php
new Index(provider: PublishedArticlesProvider::class)
```

`ProviderInterface` is autoconfigured with `lag_admin.state_provider`, so there is nothing else
to register.

> **Return a `QueryBuilder` whenever you can.** Pagination, sorting, filtering and the
> `criteria` / `orderBy` context keys are implemented as decorators that only apply to a
> Doctrine query builder. Return an array or a collection and you opt out of all of them.

## Writing a processor

```php
namespace App\State\Processor;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;

final readonly class PublishArticleProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        $data->publish();
        $this->entityManager->flush();
        $this->mailer->send(/* … */);
    }
}
```

```php
new Update(name: 'publish', processor: PublishArticleProcessor::class)
```

## How the selection works

`CompositeProvider` receives every tagged provider and picks the one whose **class name equals**
the operation's `provider` option. Same for processors. If none matches, an exception names the
resource, the operation and the application.

This means a provider is addressed by its FQCN, not by a service id or an alias.

## The decorator chain

Around the composite, decorators add cross-cutting behaviour. They run outside-in on the way
down and inside-out on the way back.

**Providers**, from outermost to innermost:

| Decorator | Priority | Does |
|---|---|---|
| `CriteriaProvider` | 310 | applies `context['criteria']` as `WHERE` clauses |
| `SortingProvider` | 300 | applies `context['sort']` / `context['order']` |
| `FilterProvider` | 220 | applies the operation filters to the query builder |
| `PaginationProvider` | 210 | wraps the query in a Pagerfanta pager |
| `ResultProvider` | 200 | executes the query for item operations |
| `UrlVariableProvider` | -250 | extracts identifiers from the request path |
| `SerializationProvider` | -220 | serializes the result when requested |
| `MappingProvider` | -210 | maps the result to the `output` class |
| `NormalizationProvider` | -200 | normalizes the result |
| `CollectionOutputProvider` | -200 | maps each item of a collection to the `output` class |

**Processors**:

| Decorator | Priority | Does |
|---|---|---|
| `MappingProcessor` | 230 | maps the submitted data from the `input` class |
| `NormalizationProcessor` | 220 | denormalizes the submitted data |
| `EventProcessor` | 200 | dispatches the data events around the processing |
| `ValidationProcessor` | 100 | validates the data, unless `validation: false` |
| `WorkflowProcessor` | 20 | applies the operation's workflow transition |
| `FlashMessageProcessor` | -200 | adds the success flash message |
| `PartialAjaxFormProcessor` | -220 | shapes the response for partial AJAX submissions |

The upshot: a custom provider does not have to care about pagination, and a custom processor
does not have to care about validation, flash messages or workflow transitions.

## Adding your own decorator

Decorate the interface, not a concrete class:

```php
// config/services.php
$services->set(App\State\Provider\AuditProvider::class)
    ->decorate(LAG\AdminBundle\State\Provider\ProviderInterface::class, priority: 250)
    ->arg('$provider', service('.inner'))
;
```

```php
final readonly class AuditProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private LoggerInterface $logger,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $this->logger->info('Providing {operation}', ['operation' => $operation->getName()]);

        return $this->provider->provide($operation, $urlVariables, $context);
    }
}
```

Pick the priority relative to the table above: higher runs closer to the outside, so a decorator
at 400 sees the query builder before pagination wraps it.

## Adding entries to the context

Context builders are decorators over `ContextBuilderInterface` and run for every operation:

```php
final readonly class TenantContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder,
        private TenantResolver $tenants,
    ) {
    }

    public function buildContext(Request $request, OperationInterface $operation, ?GridInterface $grid = null): array
    {
        $context = $this->contextBuilder->buildContext($request, $operation, $grid);
        $context['tenant'] = $this->tenants->current();

        return $context;
    }
}
```

Built-in builders provide `page`, `sort`, `order`, `partial` and `json`, on top of the
operation's own `context`.

## Next

[Forms](forms.md).
