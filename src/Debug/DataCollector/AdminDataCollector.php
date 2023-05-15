<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class AdminDataCollector extends DataCollector
{
    public function __construct(
        private ResourceRegistryInterface $registry,
        private ApplicationConfiguration $applicationConfiguration,
        private ParametersExtractorInterface $parametersExtractor,
    ) {
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $data = [
            'resources' => [],
            'application' => [],
        ];

        foreach ($this->registry->all() as $resource) {
            $data['resources'][$resource->getName()] = $this->collectResource($resource);
        }

        // When the application configuration is not defined or resolved, we can not access to the admin/menus
        // configuration
        if ($this->applicationConfiguration->isFrozen()) {
            $data['application'] = $this->applicationConfiguration->toArray();
        }

        if ($this->parametersExtractor->supports($request)) {
            $data['application']['resource'] = $this->parametersExtractor->getResourceName($request);
            $data['application']['operation'] = $this->parametersExtractor->getOperationName($request);
        }

        $this->data = $data;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getData(): array
    {
        return $this->data;
    }

    private function collectResource(AdminResource $resource): array
    {
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            $operations[$operation->getName()] = $this->collectOperation($operation);
        }

        return [
            'name' => $resource->getName(),
            'dataClass' => $resource->getDataClass(),
            'title' => $resource->getTitle(),
            'group' => $resource->getGroup(),
            'icon' => $resource->getIcon(),
            'operations' => $operations,
            'processor' => $resource->getProcessor(),
            'provider' => $resource->getProvider(),
            'identifiers' => $resource->getIdentifiers(),
            'routePattern' => $resource->getRoutePattern(),
            'routePrefix' => $resource->getRoutePrefix(),
            'translationPattern' => $resource->getTranslationPattern(),
            'translationDomain' => $resource->getTranslationDomain(),
        ];
    }

    private function collectOperation(OperationInterface $operation): array
    {
        return [
            'name' => $operation->getName(),
            'title' => $operation->getTitle(),
            'description' => $operation->getDescription(),
            'icon' => $operation->getIcon(),
            'template' => $operation->getTemplate(),
            'permissions' => $operation->getPermissions(),
            'controller' => $operation->getController(),
            'route' => $operation->getRoute(),
            'routeParameters' => $operation->getRouteParameters(),
            'methods' => $operation->getMethods(),
            'path' => $operation->getPath(),
            'targetRoute' => $operation->getTargetRoute(),
            'targetRouteParameters' => $operation->getRouteParameters(),
            // 'properties' => $operation->getProperties(),
            'formType' => $operation->getFormType(),
            'processor' => $operation->getProcessor(),
            'provider' => $operation->getProvider(),
            'identifiers' => $operation->getIdentifiers(),
            // 'contextualActions' => $operation->getContextualActions(),
            // 'itemActions' => $operation->getItemActions(),
        ];
    }
}
