<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Fixtures;

use LAG\AdminBundle\Entity\ImageAwareInterface;
use LAG\AdminBundle\Entity\ImageAwareTrait;
use LAG\AdminBundle\Entity\TimestampResourceTrait;

final class Author implements ImageAwareInterface
{
    use TimestampResourceTrait, ImageAwareTrait;

    public ?string $name = null;
}
