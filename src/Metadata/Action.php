<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProcessor\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider\ORMDataProvider;

class Action
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $icon = null,
        public readonly ?string $template = null,
        public readonly ?array $permissions = null,
        public readonly ?string $controller = null,
        public readonly ?string $route = null,
        public readonly ?array $routeParameters = null,
        public readonly ?string $path = null,
        public readonly ?string $targetRoute = null,
        public readonly ?array $targetRouteParameters = null,
        public readonly ?array $fields = null,
        public readonly ?string $formType = null,
        public readonly array $formOptions = [],
        public readonly array $collectionActions = [],
        public readonly array $itemActions = [],
        public readonly string $processor = ORMDataProcessor::class,
        public readonly string $provider = ORMDataProvider::class,
    ) {
    }

}
