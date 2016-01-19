<?php

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Add user custom repositories to the admin factory to avoid injecting the container
 */
class RepositoryComplierPass implements CompilerPassInterface
{
    /**
     * Add user custom repositories to the admin factory to avoid injecting the container
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('lag.admins')) {
            return;
        }
        $adminConfigurations = $container->getParameter('lag.admins');
        $adminFactoryDefinition = $container->getDefinition('lag.admin.factory');

        foreach ($adminConfigurations as $adminConfiguration) {
            // if a repository is configured, we add it to the admin factory
            if (array_key_exists('repository', $adminConfiguration) && $adminConfiguration['repository']) {
                $adminFactoryDefinition->addMethodCall('addRepository', [
                    $adminConfiguration['repository'],
                    new Definition($adminConfiguration['repository'])
                ]);
            }
        }
    }
}
