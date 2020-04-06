<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\OldBuildMenuEvent;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Event\Subscriber\MenuSubscriber;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuSubscriberTest extends AdminTestBase
{
    public function testServiceExists()
    {
        $this->assertServiceExists(MenuSubscriber::class);
    }

    public function testSubscribedEvents()
    {
        list($subscriber) = $this->createSubscriber(true);

        $this->assertSubscribedMethodsExists($subscriber);
    }

    public function testDefineMenuConfiguration()
    {
        list($subscriber, $registry) = $this->createSubscriber(true, [
            'left' => null,
        ]);

        $event = new MenuConfigurationEvent('left');

        $registry
            ->expects($this->once())
            ->method('keys')
            ->willReturn([
                'bamboo',
            ])
        ;

        $subscriber->defineMenuConfiguration($event);
    }

    public function testDefineMenuConfigurationWithDefined()
    {
        list($subscriber, $registry) = $this->createSubscriber(true, [
            'panda' => [
                'children' => [
                    'first' => [
                        'url' => 'google.fr',
                    ],
                ],
            ],
        ]);

        $event = new MenuConfigurationEvent('panda');

        $registry
            ->expects($this->once())
            ->method('keys')
            ->willReturn([
                'bamboo',
            ])
        ;

        $subscriber->defineMenuConfiguration($event);
    }

    public function testWithoutMenuConfiguration()
    {
        list($subscriber, $registry) = $this->createSubscriber(false);

        $event = new MenuConfigurationEvent('bamboo_menu', []);

        $registry
            ->expects($this->never())
            ->method('all')
        ;

        $subscriber->defineMenuConfiguration($event);
    }

    /**
     * @param array $adminMenuConfigurations
     *
     * @return MockObject[]|MenuSubscriber[]
     */
    private function createSubscriber(bool $menuEnabled, array $menuConfigurations = [])
    {
        $registry = $this->createMock(ResourceRegistryInterface::class);

        $subscriber = new MenuSubscriber(
            $menuEnabled,
            $registry,
            $menuConfigurations
        );

        return [
            $subscriber,
            $registry,
        ];
    }

}
