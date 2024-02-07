<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\InvalidResourceException;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private OperationFactoryInterface $operationFactory,
        private ValidatorInterface $validator,
    ) {
    }

    public function create(Resource $resource): Resource
    {
        $operations = [];
        $originalName = $resource->getName();

        foreach ($resource->getOperations() as $operation) {
            // Ensure the operation belongs to the right resource
            $operations[] = $this->operationFactory->create($operation->withResource($resource));
        }
        $resource = $resource->withOperations($operations);

        $errors = $this->validator->validate($resource, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidResourceException($resource->getName(), $errors);
        }

        // Dynamic change of the resource name is not allowed as it can cause issues in the core resource system
        if ($resource->getName() !== $originalName) {
            throw new Exception(sprintf(
                'The resource name "%s" to "%s" change is not allowed',
                $originalName,
                $resource->getName(),
            ));
        }

        return $resource;
    }
}
