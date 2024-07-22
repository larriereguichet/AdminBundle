<?php

namespace LAG\AdminBundle\Resource\Resolver;

final readonly class ClassResolver implements ClassResolverInterface
{
    public function resolveClass(string $path): ?\ReflectionClass
    {
        $class = $this->getClassName(file_get_contents($path));

        if ($class === null) {
            return null;
        }

       return new \ReflectionClass($class);
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
