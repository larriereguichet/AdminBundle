<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class AjaxContextProvider implements ContextProviderInterface
{
    public function __construct(
        private ContextProviderInterface $contextProvider
    ) {
    }

    public function getContext(OperationInterface $operation, Request $request): array
    {
        $context = $this->contextProvider->getContext($operation, $request);

        if ($request->getContentTypeFormat() === 'json' && $operation->useAjax()) {
            $context['ajax'] = true;
        }

        return $context;
    }
}
