<?php

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\Assertion\Assert;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\String\Inflector\EnglishInflector;
use function Symfony\Component\String\u;

class PathGenerator implements PathGeneratorInterface
{
    public function generatePath(OperationInterface $operation): string
    {
        Assert::operationHasResource($operation);
        $resource = $operation->getResource();

        if (!u($resource->getName())->endsWith('s')) {
            $resourceName = (new EnglishInflector())->pluralize($resource->getName())[0];
        } else {
            $resourceName = $resource->getName();
        }
        $path = u($resource->getRoutePrefix())
            ->replace('{resourceName}', $resourceName)
        ;

        if ($operation->getPath()) {
            $path
                ->ensureEnd('/')
                ->append($operation->getPath())
            ;
        } else {
            foreach ($operation->getRouteParameters() as $parameter => $requirement) {
                $path = $path
                    ->append('/')
                    ->append('{'.$parameter.'}')
                ;
            }

        }
        $operationPath = u($operation->getPath() ?? '');

        if ($operationPath->length() > 0) {
            $operationPath = $operationPath->ensureStart('/');
        }

        $path = $path->append($operationPath->toString());

        if ($path->endsWith('/')) {
            $path = $path->beforeLast('/');
        }

        return $path->toString();
    }
}
