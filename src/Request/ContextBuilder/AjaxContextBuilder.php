<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class AjaxContextBuilder implements ContextBuilderInterface
{
    public function supports(OperationInterface $operation, Request $request): bool
    {
        return $operation->hasAjax();
    }

    public function buildContext(OperationInterface $operation, Request $request): array
    {
        $context = [];

        if ($request->getContentTypeFormat() === 'json') {
            $context['json'] = true;
        }

        return $context;
    }
}
