<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

class AjaxContextProvider implements ContextProviderInterface
{
    public function getContext(OperationInterface $operation, Request $request): array
    {
        if ($request->getContentTypeFormat() === 'json' && $operation->useAjax()) {
            return ['ajax' => true];
        }

        return [];
    }
}
