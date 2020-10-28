<?php

namespace LAG\AdminBundle\Tests\Fixtures\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use function Symfony\Component\String\u;
use Symfony\Component\Yaml\Yaml;

class PublicServiceCompilerPass implements CompilerPassInterface
{
    private string $configPath;

    public function __construct(string $configPath)
    {
        $this->configPath = $configPath;
    }

    public function process(ContainerBuilder $container)
    {
        $finder = new Finder();
        $finder
            ->in($this->configPath)
            ->name('*.yaml')
        ;

        foreach ($finder as $fileInfo) {
            $data = Yaml::parse(file_get_contents($fileInfo->getRealPath()), Yaml::PARSE_CUSTOM_TAGS);

            if (empty($data['services'])) {
                continue;
            }

            foreach ($data['services'] as $id => $service) {
                if ($id === '_defaults') {
                    continue;
                }

                if (u($id)->endsWith('Interface')) {
                    $alias = $container->getAlias($id);
                    $alias->setPublic(true);
                } else {
                    $definition = $container->getDefinition($id);
                    $definition->setPublic(true);
                }
            }
        }
    }
}
