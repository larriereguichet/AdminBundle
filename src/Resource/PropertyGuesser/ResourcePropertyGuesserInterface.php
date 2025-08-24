<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\PropertyGuesser;

use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\Resource;

interface ResourcePropertyGuesserInterface
{
    /** @return array<int, PropertyInterface> */
    public function guessProperties(Resource $resource): array;
}
