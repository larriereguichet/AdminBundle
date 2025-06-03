<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminDataCollector extends AbstractDataCollector
{
    public function __construct(
        private readonly OperationContextInterface $operationContext,
    ) {
    }

    public static function getTemplate(): ?string
    {
        return '@LAGAdmin/debug/template.html.twig';
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        if ($this->operationContext->hasOperation()) {
            $operation = $this->operationContext->getOperation();
            $this->data['application'] = $operation->getResource()->getApplication();
            $this->data['resource'] = $operation->getResource()->getName();
            $this->data['operation'] = $operation->getShortName();
        }
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
