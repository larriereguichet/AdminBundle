<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection\Locator;

use Symfony\Component\Finder\Finder;

final readonly class ClassLocator
{
    /**
     * @param iterable<string> $paths
     *
     * @return iterable<class-string>
     */
    public function locateClassesByPaths(iterable $paths): iterable
    {
        foreach ($paths as $resourceDirectory) {
            $resources = $this->locateClasses($resourceDirectory);

            foreach ($resources as $className) {
                yield $className;
            }
        }
    }

    /** @return iterable<class-string> */
    public function locateClasses(string $path): iterable
    {
        $finder = new Finder();
        $finder->files()->in($path)->name('*.php')->sortByName(true);

        foreach ($finder as $file) {
            $fileContent = file_get_contents((string) $file->getRealPath());

            if (false === $fileContent) {
                throw new \RuntimeException(\sprintf('Unable to read "%s" file', $file->getRealPath()));
            }
            preg_match('/namespace (.+);/', $fileContent, $matches);
            $namespace = $matches[1] ?? null;

            if (!preg_match('/class +([^{ ]+)/', $fileContent, $matches)) {
                // no class found
                continue;
            }
            $className = trim($matches[1]);

            if (null !== $namespace) {
                yield $namespace.'\\'.$className;
            } else {
                yield $className;
            }
        }
    }
}
