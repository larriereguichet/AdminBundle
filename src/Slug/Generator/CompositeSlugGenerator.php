<?php

namespace LAG\AdminBundle\Slug\Generator;

use LAG\AdminBundle\Entity\Mapping\Sluggable;
use LAG\AdminBundle\Exception\Exception;

class CompositeSlugGenerator implements SlugGeneratorInterface
{
    public function __construct(
        /** @var array<string, SlugGeneratorInterface> */
        private array $generators,
    )
    {
    }

    public function generateSlug(string $source, string $generatorName = 'default'): string
    {
        foreach ($this->generators as $name => $generator) {
            if ($name === $generatorName) {
                return $generator->generateSlug($source, $generatorName);
            }
        }

        throw new Exception(sprintf('There is no slug generator with name "%s"', $generatorName));
    }

    public function getName(): string
    {
        return 'composite';
    }
}
