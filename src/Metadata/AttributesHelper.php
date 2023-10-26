<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Entity\Mapping\Sluggable;

class AttributesHelper
{
    public static function getReflectionClassesFromDirectories(string $path): \Iterator
    {
        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            ),
            '/^.+\.php$/i',
            \RegexIterator::GET_MATCH
        );

        foreach ($iterator as $file) {
            $sourceFile = $file[0];

            if (!preg_match('(^phar:)i', $sourceFile)) {
                $sourceFile = realpath($sourceFile);
            }

            try {
                require_once $sourceFile;
            } catch (\Throwable $t) {
                // invalid PHP file (example: missing parent class)
                continue;
            }

            $includedFiles[$sourceFile] = true;
        }
        $declared = array_merge(get_declared_classes(), get_declared_interfaces());

        foreach ($declared as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $sourceFile = $reflectionClass->getFileName();

            if (isset($includedFiles[$sourceFile])) {
                yield $className => $reflectionClass;
            }
        }
    }

    public static function getAttributes(string $sourceClass, string $attributeClass): array
    {
        $reflectionClass = new \ReflectionClass($sourceClass);
        $attributes = [];

        foreach ($reflectionClass->getAttributes($attributeClass) as $attribute) {
            $attributes[] = $attribute->newInstance();
        }

        return $attributes;
    }
}
