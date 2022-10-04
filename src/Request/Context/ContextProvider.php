<?php

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

class ContextProvider implements ContextProviderInterface
{

    public function getContext(OperationInterface $operation, Request $request): array
    {
        $context = [];

        if ($operation instanceof CollectionOperationInterface) {
            $pageParameter = $operation->getPageParameter();

            if ($request->query->has($pageParameter)) {
                $context[$pageParameter] = $request->query->get($pageParameter);
            }
        }

        return $context;
    }
}
