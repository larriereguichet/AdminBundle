<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Exception\InvalidResourceException;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ValidationResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private ResourceFactoryInterface $resourceFactory,
        private ValidatorInterface $validator,
    ) {
    }

    public function create(string $resourceName): Resource
    {
        $resource = $this->resourceFactory->create($resourceName);
        $errors = $this->validator->validate($resource, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidResourceException($resource->getFullName(), $errors);
        }

        return $resource;
    }
}
