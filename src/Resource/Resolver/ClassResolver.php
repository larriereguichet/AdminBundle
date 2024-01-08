<?php

namespace LAG\AdminBundle\Resource\Resolver;

use Symfony\Component\Finder\Finder;

class ClassResolver implements ClassResolverInterface
{
    public function resolveClasses(string $directory): array
    {
        $finder = new Finder();
        $finder->files()
            ->in($directory)
            ->name('*.php')
            ->sortByName(true)
        ;
        $classes = [];

        foreach ($finder as $fileInfo) {
            if (!$fileInfo->isReadable()) {
                continue;
            }
            $class = $this->getClassName($fileInfo->getContents());

            if ($class === null) {
                continue;
            }
            $classes[] = new \ReflectionClass($class);
        }

        return $classes;
    }

    private function getClassName(string $content): ?string
    {
        preg_match('/namespace (.+);/', $content, $matches);

        $namespace = $matches[1] ?? null;

        if (!preg_match('/class +([^{ ]+)/', $content, $matches)) {
            // no class found
            return null;
        }
        $className = trim($matches[1]);

        if (null !== $namespace) {
            return $namespace . '\\' . $className;
        }

        return $className;
    }
}
