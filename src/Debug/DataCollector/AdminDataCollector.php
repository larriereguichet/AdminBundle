<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[When('dev')]
class AdminDataCollector extends AbstractDataCollector
{
    public function __construct(
        private array $applicationConfiguration,
        private ResourceRegistryInterface $registry,
        private ParametersExtractorInterface $parametersExtractor,
    ) {
    }

    public static function getTemplate(): ?string
    {
        return '@LAGAdmin/debug/template.html.twig';
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $data['resources'] = [];
        $data['application'] = $this->applicationConfiguration;

        $data['request']['application'] = $this->parametersExtractor->getApplicationName($request);
        $data['request']['resource'] = $this->parametersExtractor->getResourceName($request);
        $data['request']['operation'] = $this->parametersExtractor->getOperationName($request);

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
}
