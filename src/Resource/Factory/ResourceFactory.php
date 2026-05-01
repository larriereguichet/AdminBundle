<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Exception\InvalidResourceException;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private ResourceMetadataFactoryInterface $metadataFactory,
        private ValidatorInterface $validator,
    ) {
    }

    public function create(string $resourceName): ResourceInterface
    {
        $resource = $this->metadataFactory->createMetadata($resourceName);
        $errors = $this->validator->validate($resource, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidResourceException($resource->getName(), $errors);
        }

        return $resource;
    }
}
