<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminDataCollector extends AbstractDataCollector
{
    public function __construct(
        private readonly ResourceRegistryInterface $registry,
        private readonly ResourceParametersExtractorInterface $parametersExtractor,
    ) {
    }

    public static function getTemplate(): ?string
    {
        return '@LAGAdmin/debug/template.html.twig';
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $data['application'] = $this->parametersExtractor->getApplicationName($request);
        $data['resource'] = $this->parametersExtractor->getResourceName($request);
        $data['operation'] = $this->parametersExtractor->getOperationName($request);
        $data['resources'] = $this->collectResources();

        $this->data = $data;
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getData(): array
    {
        return $this->data;
    }

    private function collectResources(): array
    {
        $data = [];

        foreach ($this->registry->all() as $resource) {
            $data[$resource->getName()] = $resource;
        }

        return $data;
    }
}
