<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class PartialContextBuilder implements ContextBuilderInterface
{
    public function supports(OperationInterface $operation, Request $request): bool
    {
        return true;
    }

    public function buildContext(OperationInterface $operation, Request $request): array
    {
        $context['partial'] = false;

        if ($request->query->getBoolean('_partial') === true) {
            $context['partial'] = true;
        }

        return $context;
    }
}
