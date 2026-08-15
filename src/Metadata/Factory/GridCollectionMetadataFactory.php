<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Config\LAGAdminBuilder;
use Symfony\Component\Finder\Finder;

final class GridCollectionMetadataFactory implements GridCollectionMetadataFactoryInterface
{
    /** @var array<string, mixed>|null */
    private ?array $cache = null;

    /** @param array<string> $paths */
    public function __construct(
        private readonly array $paths,
        private readonly string $kernelEnvironment,
    ) {
    }

    public function createMetadata(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $builder = new LAGAdminBuilder($this->kernelEnvironment);

        foreach ($this->paths as $path) {
            $finder = new Finder()
                ->files()
                ->name('*.php')
                ->sortByName(true)
                ->in($path)
                ->contains('return static function')
            ;

            foreach ($finder as $file) {
                if ($this->isClassFile($file->getRealPath())) {
                    continue;
                }
                // The closure forbids access to the private scope in the included file
                $loader = \Closure::bind(static fn ($filePath) => include $filePath, null, null);

                try {
                    $callback = $loader($file->getRealPath());
                } catch (\Throwable) {
                    continue;
                }

                if (!\is_callable($callback)) {
                    continue;
                }

                $callback($builder);
            }
        }

        return $this->cache = $builder->getGrids();
    }

    private function isClassFile(string $filePath): bool
    {
        $content = file_get_contents($filePath);

        return (bool) preg_match('/^\s*(class|interface|trait|enum)\s+/m', $content);
    }
}
