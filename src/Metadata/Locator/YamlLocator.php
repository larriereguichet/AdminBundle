<?php

namespace LAG\AdminBundle\Metadata\Locator;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\AdminResource;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class YamlLocator implements MetadataLocatorInterface
{
    public function locateCollection(string $resourceDirectory): iterable
    {
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($resourceDirectory) || !is_dir($resourceDirectory)) {
            throw new Exception(sprintf(
                'The resources path %s does not exists or is not a directory',
                $resourceDirectory
            ));
        }
        $finder = new Finder();
        $finder
            ->files()
            ->name('*.yaml')
            ->in($resourceDirectory)
        ;
        $resources = [];

        foreach ($finder as $fileInfo) {
            $yaml = Yaml::parse(file_get_contents($fileInfo->getRealPath()), Yaml::PARSE_CUSTOM_TAGS);

            foreach ($yaml as $name => $configuration) {
                $resource = (new MapperBuilder())
                    ->mapper()
                    ->map(AdminResource::class, Source::array($configuration ?? []))
                ;
                $resource = $resource->withName($name);
                $resources[] = $resource;
            }
        }

        return $resources;
    }
}
