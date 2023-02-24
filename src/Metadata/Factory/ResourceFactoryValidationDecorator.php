<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Exception\Validation\InvalidAdminException;
use LAG\AdminBundle\Exception\Validation\InvalidOperationException;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Validation\Constraint\AdminValid;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResourceFactoryValidationDecorator implements ResourceFactoryInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private ResourceFactoryInterface $decorated,
    ) {
    }

    public function create(AdminResource $definition): AdminResource
    {
        $resource = $this->decorated->create($definition);
        $errors = $this->validator->validate($resource, [new AdminValid(), new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidAdminException($resource->getName(), $errors);
        }

        foreach ($resource->getOperations() as $operation) {
            $errors = $this->validator->validate($operation, [new Valid()]);

            if ($errors->count() > 0) {
                throw new InvalidOperationException($resource->getName(), $operation->getName(), $errors);
            }
        }

        return $resource;
    }
}
