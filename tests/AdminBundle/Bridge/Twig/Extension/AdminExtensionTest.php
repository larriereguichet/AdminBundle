<?php

namespace LAG\AdminBundle\Tests\Bridge\Twig\Extension;

use LAG\AdminBundle\Bridge\Twig\Extension\AdminExtension;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Routing\RouterInterface;
use Twig\TwigFunction;

class AdminExtensionTest extends AdminTestBase
{
    public function testGetFunctions()
    {
        list($extension) = $this->createExtension();

        $functions = $extension->getFunctions();

        /** @var TwigFunction $function */
        foreach ($functions as $function) {
            $this->assertContains($function->getName(), [
                'admin_config',
                'admin_menu_action',
                'admin_url',
                'admin_action_allowed',
            ]);
            $this->assertTrue(method_exists($extension, $function->getCallable()[1]));
        }
    }

    /**
     * @return AdminExtension[]|MockObject[]
     */
    private function createExtension(): array
    {
        $storage = $this->createMock(ApplicationConfigurationStorage::class);
        $router = $this->createMock(RouterInterface::class);
        $factory = $this->createMock(ConfigurationFactory::class);

        $extension = new AdminExtension($storage, $router, $factory);

        return [
            $extension,
            $storage,
            $router,
            $factory,
        ];
    }
}
