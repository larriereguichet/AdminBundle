<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Exception\InvalidResourceException;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Initializer\ResourceInitializerInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private DefinitionFactoryInterface $definitionFactory,
        private ResourceInitializerInterface $resourceInitializer,
        private ValidatorInterface $validator,
    ) {
    }

    public function create(string $resourceName): Resource
    {
        $definition = $this->definitionFactory->createResourceDefinition($resourceName);
        $resource = $this->resourceInitializer->initializeResource($definition);
        $errors = $this->validator->validate($resource, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidResourceException($resource->getName(), $errors);
        }

        return $resource;
    }
}
