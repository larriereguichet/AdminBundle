<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class AjaxContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder
    ) {
    }

    public function buildContext(OperationInterface $operation, Request $request): array
    {
        $context = $this->contextBuilder->buildContext($operation, $request);

        if (!$operation->useAjax()) {
            return $context;
        }

        if ($request->getContentTypeFormat() === 'json') {
            $context['json'] = true;
        }

        return $context;
    }
}
