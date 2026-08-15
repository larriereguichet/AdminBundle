<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity\Mapping;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
readonly class Sluggable
{
    /**
     * @param string|string[] $sourceProperties
     */
    public function __construct(
        public string|array $sourceProperties,
        public string $targetProperty = 'slug',
        public string $generator = 'default',
    ) {
    }
}
