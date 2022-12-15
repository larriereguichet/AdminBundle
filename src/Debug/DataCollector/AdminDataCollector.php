<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
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
            $reflection = new \ReflectionClass($resource);

            foreach ($reflection->getProperties() as $reflectionProperty) {
                if ($reflectionProperty->isInitialized($resource)) {
                    $data['resources'][$resource->getName()][$reflectionProperty->getName()] = $reflectionProperty->getValue($resource);
                }
            }
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

    public function reset()
    {
        $this->data = [];
    }

    public function getData(): array
    {
        return $this->data;
    }
}
