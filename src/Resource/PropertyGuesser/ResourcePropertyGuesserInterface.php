<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\PropertyGuesser;

use LAG\AdminBundle\Resource\Metadata\Resource;

interface ResourcePropertyGuesserInterface
{
    public function guessProperties(Resource $resource): iterable;
}
