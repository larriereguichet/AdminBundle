<?php

namespace LAG\AdminBundle;

use LAG\AdminBundle\DependencyInjection\FieldCompilerPass;
use LAG\AdminBundle\DependencyInjection\ManagerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LAGAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // register field compiler pass
        $container->addCompilerPass(new FieldCompilerPass());
        $container->addCompilerPass(new ManagerCompilerPass());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $class = $this->getContainerExtensionClass();

            if (class_exists($class)) {
                $extension = new $class();

                if (!$extension instanceof ExtensionInterface) {
                    throw new \LogicException(sprintf('Extension %s must implement Symfony\Component\DependencyInjection\Extension\ExtensionInterface.', $class));
                }
                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }
        if ($this->extension) {
            return $this->extension;
        }
    }
}
