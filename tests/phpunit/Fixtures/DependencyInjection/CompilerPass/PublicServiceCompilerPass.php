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

        foreach ($container->getDefinitions() as $service => $definition) {
            if (u($service)->startsWith('LAG')) {
                $definition->setPublic(true);
            }
        }
    }
}
