# State

The `AdminBundle` use providers and processors to alter data. Providers are responsible to retrieve data, and processors
to persist data.

## Providers

A provider is a service implementing `LAG\AdminBundle\State\Provider\ProviderInterface`. A provider is responsible to 
retrieve or transform data to the view. It can be decorated to add specific behavior like filtering or pagination. Each 
operation should have a provider. The provider can be a native provider from `AdminBundle` pr a custom provider.

The provider should retrieve data before it is passed to the view. Unlike the processor, the provider is required for 
each operation. Usually, it will use the Doctrine ORM to make  a database query and retrieve one or several objects, 
according to the operation type.

### Custom provider

You can use a custom provider to retrieve data when the native provider does not fit your needs. A provider is a Symfony 
service which implements `LAG\AdminBundle\State\Provider\ProviderInterface`. 

> If the service is autoconfigured, there is nothing more to do. If not, you should add the `lag_admin.state_provider` 
> service tag.

```php
<?php

declare(strict_types=1);

namespace App\State\Provider;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

final readonly class MyCustomProvider implements ProviderInterface
{
    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        // Do something to retrieve your data...
    }
}
```

### Pagination

When using a custom provider, you may required pagination. To use the native pagination provided by the `AdminBundle`, 
your custom provider should return a Doctrine ORM `QueryBuilder` or `Query`. The `PaginationProvider` will decorated your 
provider and add the pagination data.

```php
<?php

// ...
use Doctrine\ORM\QueryBuilder;

final readonly class MyCustomProvider implements ProviderInterface
{
    public function __construct(
        private MyRepository $repository,
    ) {
    }
    
    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): QueryBuilder
    {
        return $this->repository
            ->createQueryBuilder('my_entity')
            // do your complex query here
        ;
    }
}
```

You can also handle the pagination yourself :

```php
<?php

// ...
use Doctrine\ORM\QueryBuilder;

final readonly class MyCustomProvider implements ProviderInterface
{
    public function __construct(
        private MyRepository $repository,
    ) {
    }
    
    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): QueryBuilder
    {
        $queryBuilder = $this->repository
            ->createQueryBuilder('my_entity')
            // do your complex query here
        ;
        
        $pager = new Pagerfanta(new QueryAdapter($queryBuilder, true, true));
        $pager->setMaxPerPage($operation->getItemsPerPage()); // Retrieve the maximum items per page from the current operation
        $pager->setCurrentPage($context['page'] ?? 1); // The context is automatically filled with the current page

        return $pager;
    }
}
```

## Processors

A provider is a service implementing `LAG\AdminBundle\State\Processor\ProcessorInterface`. A processor is responsible to
transform ans persist data (into database for instance). It is usually used when a form is submitted.

Each operation could have a processor. The provider can be a native processor from `AdminBundle` pr a custom processor.

Usually, the processor will persist one object from a Symfony form to the database using the Doctrine ORM repositories.

### Custom processors

You can use a custom processor to persist data when the native processor does not fit your needs. A processor is a 
Symfony service which implements `LAG\AdminBundle\State\Processor\ProcessorInterface`.

> If the service is autoconfigured, there is nothing more to do. If not, you should add the `lag_admin.state_processor`
> service tag.

```php
<?php

declare(strict_types=1);

namespace App\State\Provider;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;

final readonly class MyCustomProcessor implements ProcessorInterface
{
    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        // Do something to persist your data...
    }
}
```
