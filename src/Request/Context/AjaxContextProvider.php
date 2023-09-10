<?php

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

class AjaxContextProvider implements ContextProviderInterface
{
    public function getContext(OperationInterface $operation, Request $request): array
    {
        if ($request->getContentTypeFormat() === 'application/json' && $operation->useAjax()) {
            return ['ajax' => true];
        }

        return [];
    }
}
