<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Exception\InvalidResourceException;
use LAG\AdminBundle\Resource\Metadata\Resource;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private OperationFactoryInterface $operationFactory,
        private ValidatorInterface $validator,
    ) {
    }

    public function create(Resource $definition): Resource
    {
        $operations = [];

        foreach ($definition->getOperations() as $operation) {
            // Ensure the operation belongs to the right resource
            $operations[] = $this->operationFactory->create($operation->withResource($definition));
        }
        $resource = $definition->withOperations($operations);

        $errors = $this->validator->validate($resource, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidResourceException($resource->getName(), $resource->getApplication(), $errors);
        }

        return $resource;
    }
}
