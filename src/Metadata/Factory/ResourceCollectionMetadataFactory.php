<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Config\LAGAdminBuilder;
use Symfony\Component\Finder\Finder;

/**
 * Build resource from php configuration files defined in the bundle mapping paths.
 */
final readonly class ResourceCollectionMetadataFactory implements ResourceCollectionMetadataFactoryInterface
{
    /** @param array<string> $paths */
    public function __construct(
        private array $paths,
        private string $kernelEnvironment,
    ) {
    }

    public function createMetadata(): array
    {
        $builder = new LAGAdminBuilder($this->kernelEnvironment);

        foreach ($this->paths as $path) {
            $finder = new Finder()
                ->files()
                ->name('*.php')
                ->sortByName(true)
                ->in($path)
            ;

            foreach ($finder as $file) {
                // The closure forbids access to the private scope in the included file
                $callback = \Closure::bind(static fn($filePath) => include $filePath, null, null);

                try {
                    $callback = $callback($file->getRealPath());
                } catch (\Throwable) {
                }

                if (!\is_callable($callback)) {
                    continue;
                }
                $callback($builder);
            }
        }

        return $builder->getResources();
    }
}
