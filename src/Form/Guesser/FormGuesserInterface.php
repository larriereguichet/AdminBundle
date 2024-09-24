<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Guesser;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

/**
 * Guess form type for the given resource property. If a form type can not be guessed, null is returned. This is useful
 * when no form type is defined for an Update operation for instance.
 */
interface FormGuesserInterface
{
    public function guessFormType(OperationInterface $operation, PropertyInterface $property): ?string;

    public function guessFormOptions(OperationInterface $operation, PropertyInterface $property): array;
}
