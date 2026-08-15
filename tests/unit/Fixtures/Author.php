<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Fixtures;

use LAG\AdminBundle\Entity\TimestampResourceTrait;
use LAG\AdminBundle\Image\ImageAwareInterface;
use LAG\AdminBundle\Image\ImageAwareTrait;

final class Author implements ImageAwareInterface
{
    use ImageAwareTrait;
    use TimestampResourceTrait;

    public ?string $name = null;
}
