<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\Resource\Loader;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ResourceLoader
{
    /**
     * Load admins configuration in the yaml files found in the given resource path. An exception will be thrown if the
     * path is invalid.
     *
     * @throws Exception
     */
    public function load(string $resourcesPath): array
    {
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($resourcesPath) || !is_dir($resourcesPath)) {
            throw new Exception(sprintf('The resources path %s does not exists or is not a directory', $resourcesPath));
        }
        $finder = new Finder();
        $finder
            ->files()
            ->name('*.yaml')
            ->in($resourcesPath)
        ;
        $data = [];

        foreach ($finder as $fileInfo) {
            $yaml = Yaml::parse(file_get_contents($fileInfo->getRealPath()), Yaml::PARSE_CUSTOM_TAGS);

            foreach ($yaml as $name => $admin) {
                $data[$name] = $admin;
            }
        }

        return $data;
    }
}
