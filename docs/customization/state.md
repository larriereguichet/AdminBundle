# Custom state

Recipes for reading and writing data your own way. The concepts are in
[Providers and processors](../concepts/providers-and-processors.md); this page is the cookbook.

## Restrict a listing to the current user's records

```php
final readonly class MyArticlesProvider implements ProviderInterface
{
    public function __construct(
        private ArticleRepository $repository,
        private Security $security,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        return $this->repository
            ->createQueryBuilder('article')
            ->andWhere('article.author = :author')
            ->setParameter('author', $this->security->getUser())
        ;
    }
}
```

```php
new Index(provider: MyArticlesProvider::class, grid: 'admin_articles')
```

Returning the query builder — not the results — keeps pagination, sorting and filtering
working.

## Share one provider across several operations

Parameterize with the operation context instead of writing a provider per variant:

```php
new Index(name: 'drafts',    context: ['status' => 'draft'],     provider: StatusProvider::class),
new Index(name: 'published', context: ['status' => 'published'], provider: StatusProvider::class),
```

```php
public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
{
    return $this->repository->createQueryBuilder('a')
        ->andWhere('a.status = :status')
        ->setParameter('status', $context['status'])
    ;
}
```

For a plain equality filter you do not even need the provider — `context: ['criteria' => ['status' => 'draft']]`
is applied by `CriteriaProvider`.

## Load a record by something other than its identifier

```php
new Show(
    path: '/{slug}',
    identifiers: ['slug'],
    provider: SlugArticleProvider::class,
)
```

```php
public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
{
    return $this->repository->findOneBy(['slug' => $urlVariables['slug']]);
}
```

`$urlVariables` is filled by `UrlVariableProvider` from the request path, using the operation's
identifiers.

Note that `ORMProvider` already does this: it adds a `WHERE` clause for every identifier present
in the URL, so `identifiers: ['slug']` alone is often enough.

## A screen with no entity behind it

A report, a dashboard: a named `Show` with a provider returning whatever the template needs.

```php
new Show(
    name: 'sales_report',
    path: '/reports/sales',
    template: 'admin/reports/sales.html.twig',
    provider: SalesReportProvider::class,
)
```

```php
public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
{
    return ['byMonth' => $this->reports->salesByMonth()];
}
```

## Do something extra on save

Two options.

**A processor**, when the operation *is* the action:

```php
final readonly class PublishArticleProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $bus,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        $data->publish();
        $this->entityManager->flush();
        $this->bus->dispatch(new ArticlePublished($data->getId()));
    }
}
```

**A listener**, when the behaviour is a side effect of any save:

```php
#[AsEventListener(event: 'admin.article.data_processed')]
final readonly class InvalidateCacheListener
{
    public function __invoke(DataEvent $event): void
    {
        $this->cache->invalidateTags(['articles']);
    }
}
```

A processor replaces the persistence; a listener runs alongside it. If your processor replaces
`ORMProcessor` you are responsible for persisting.

## Add behaviour to every operation

Decorate the interface:

```php
$services->set(App\State\Processor\AuditProcessor::class)
    ->decorate(LAG\AdminBundle\State\Processor\ProcessorInterface::class, priority: 150)
    ->arg('$processor', service('.inner'))
;
```

```php
final readonly class AuditProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private AuditLogger $logger,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        $this->processor->process($data, $operation, $urlVariables, $context);
        $this->logger->log($operation->getName(), $data);
    }
}
```

Priority places you in the chain — see the tables in
[Providers and processors](../concepts/providers-and-processors.md#the-decorator-chain).
Priority 150 puts this decorator between validation (100) and the event processor (200), so it
only runs on data that has been validated.

> A decorator implementing `ProviderInterface` or `ProcessorInterface` is **also**
> autoconfigured with the state tag, which would make it selectable as an operation's provider.
> That is harmless but confusing; add `->tag('container.excluded')`-style exclusions or simply
> never name it in an operation.

## Return a different response

Neither a provider nor a processor: a `ResourceControllerEvent` listener.

```php
#[AsEventListener(event: 'admin.invoice.controller')]
final readonly class DownloadInvoiceListener
{
    public function __invoke(ResourceControllerEvent $event): void
    {
        if ($event->getOperation()->getShortName() !== 'download') {
            return;
        }

        $event->setResponse(new BinaryFileResponse($this->pdf->render($event->getData())));
    }
}
```

## Map to and from DTOs

With `symfony/object-mapper` installed as a non-dev dependency:

```php
new Create(
    input: CreateArticleInput::class,
    output: ArticleOutput::class,
)
```

`MappingProvider` maps the provided data to `output`, `MappingProcessor` maps the submitted
`input` back to the resource class. Both services are removed from the container when no object
mapper is available, so the options are silently ignored rather than breaking the build.
