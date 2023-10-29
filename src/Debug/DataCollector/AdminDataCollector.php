<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
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
        $data = [];
        $data['resources'] = $this->registry->all();

        if ($this->applicationConfiguration->isFrozen()) {
            $data['application'] = $this->applicationConfiguration->toArray();
        }

        if ($this->parametersExtractor->supports($request)) {
            $data['request']['resource'] = $this->parametersExtractor->getResourceName($request);
            $data['request']['operation'] = $this->parametersExtractor->getOperationName($request);
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
}
