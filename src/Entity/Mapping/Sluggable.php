<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity\Mapping;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
readonly class Sluggable
{
    public function __construct(
        public string $sourceProperty,
        public string $targetProperty = 'slug',
        public string $generator = 'default',
    ) {
    }
}
