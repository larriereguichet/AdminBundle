<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Event\Subscriber;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use LAG\AdminBundle\Action\Event\ActionEvents;
use LAG\AdminBundle\Action\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Event\AdminCreateEvent;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Subscriber\ExtraConfigurationSubscriber;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestSimpleEntity;

class ExtraConfigurationSubscriberTest extends AdminTestBase
{
    /**
     * ExtraConfigurationSubscriber SHOULD subscribe to the Admin creation and the Action creation event.
     */
    public function testGetSubscribedEvents()
    {
        $subscribedEvents = ExtraConfigurationSubscriber::getSubscribedEvents();
        
        $this->assertArrayHasKey(AdminEvents::ADMIN_CREATE, $subscribedEvents);
        $this->assertArrayHasKey(ActionEvents::BEFORE_CONFIGURATION, $subscribedEvents);
        $this->assertContains('beforeActionConfiguration', $subscribedEvents);
        $this->assertContains('adminCreate', $subscribedEvents);
    }
    
    /**
     * With extra configuration disabled, adminCreate method SHOULD not modify the configuration.
     */
    public function testAdminCreateWithoutExtraConfiguration()
    {
        $doctrine = $this->getMockWithoutConstructor(Registry::class);
        $event = $this->getMockWithoutConstructor(AdminCreateEvent::class);
        $event
            ->expects($this->never())
            ->method('setAdminConfiguration')
        ;
        $storage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
        
        $subscriber = new ExtraConfigurationSubscriber(
            false,
            $doctrine,
            $storage
        );
        $subscriber->adminCreate($event);
    }
    
    /**
     * with extra configuration enabled, adminCreate method SHOULD fill action configuration if it is empty.
     */
    public function testAdminCreateWithExtraConfiguration()
    {
        $doctrine = $this->getMockWithoutConstructor(Registry::class);
        $event = $this->getMockWithoutConstructor(AdminCreateEvent::class);
        $event
            ->expects($this->once())
            ->method('getAdminConfiguration')
            ->willReturn([
                'my_config' => [
                    'a_normal_key' => 'a_value',
                ],
            ])
        ;
        $event
            ->expects($this->once())
            ->method('setAdminConfiguration')
            ->with([
                'my_config' => [
                    'a_normal_key' => 'a_value',
                ],
                'actions' => [
                    'create' => [],
                    'list' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ])
        ;
        $storage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
        
        $subscriber = new ExtraConfigurationSubscriber(
            true,
            $doctrine,
            $storage
        );
        $subscriber->adminCreate($event);
    }

    public function testMenuConfiguration()
    {
        $doctrine = $this->getMockWithoutConstructor(Registry::class);
        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['actions', [
                    'list',
                    'create',
                    'edit',
                    'delete',
                ]],
            ])
        ;
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $admin
            ->method('getName')
            ->willReturn('my_admin')
        ;
        
        $event = $this->getMockWithoutConstructor(BeforeConfigurationEvent::class);
        $event
            ->expects($this->once())
            ->method('getActionConfiguration')
            ->willReturn([
                'fields' => [
                    'test' => []
                ]
            ])
        ;
        $event
            ->expects($this->once())
            ->method('setActionConfiguration')
            ->with([
                'menus' => [
                    'top' => [
                        'items' => [
                            'create' => [
                                'admin' => 'my_admin',
                                'action' => 'create',
                                'icon' => 'fa fa-plus',
                            ]
                        ]
                    ]
                ],
                'fields' => [
                    'test' => [],
                ]
            ])
        ;
        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $event
            ->expects($this->exactly(3))
            ->method('getActionName')
            ->willReturn('list')
        ;
        $storage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
        
        $subscriber = new ExtraConfigurationSubscriber(
            true,
            $doctrine,
            $storage
        );
        $subscriber->beforeActionConfiguration($event);
    }

    public function testLinkedActionsForListAction()
    {
        $applicationConfiguration = $this->getMockWithoutConstructor(ApplicationConfiguration::class);
        $applicationConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['translation', [
                    'pattern' => '{admin}.{key}'
                ]],
            ])
        ;
        
        $classMetadata = $this->getMockWithoutConstructor(ClassMetadata::class);
        $classMetadata
            ->method('getFieldNames')
            ->willReturn([
                'id'
            ])
        ;
        $classMetadata
            ->method('getTypeOfField')
            ->willReturn('string')
        ;

        $metadataFactory = $this->getMockWithoutConstructor(ClassMetadataFactory::class);
        $metadataFactory
            ->method('getMetadataFor')
            ->willReturn($classMetadata)
        ;

        $entityManager = $this->getMockWithoutConstructor(EntityManager::class);
        $entityManager
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory)
        ;

        $doctrine = $this->getMockWithoutConstructor(Registry::class);
        $doctrine
            ->method('getManager')
            ->willReturn($entityManager)
        ;
    
        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['actions', [
                    'list' => [],
                    'create' => [],
                    'edit' => [],
                    'delete' => [],
                ]],
            ])
        ;
        $adminConfiguration
            ->expects($this->once())
            ->method('isResolved')
            ->willReturn(true)
        ;
        
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->exactly(3))
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $admin
            ->method('getName')
            ->willReturn('my_admin')
        ;
        
        $event = $this->getMockWithoutConstructor(BeforeConfigurationEvent::class);
        $event
            ->expects($this->atLeast(1))
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $event
            ->expects($this->once())
            ->method('getActionConfiguration')
            ->willReturn([])
        ;
        $event
            ->method('getActionName')
            ->willReturn('list')
        ;
        $storage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
        $storage
            ->expects($this->once())
            ->method('getApplicationConfiguration')
            ->willReturn($applicationConfiguration)
        ;

        $subscriber = new ExtraConfigurationSubscriber(
            true,
            $doctrine,
            $storage
        );
        $subscriber->beforeActionConfiguration($event);
    }

    public function testBeforeConfigurationWithoutExtraConfigurationEnabled()
    {
        $doctrine = $this->getMockWithoutConstructor(Registry::class);
        $event = $this->getMockWithoutConstructor(BeforeConfigurationEvent::class);
        $event
            ->expects($this->never())
            ->method('getActionConfiguration')
        ;
        $storage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
    
        $subscriber = new ExtraConfigurationSubscriber(
            false,
            $doctrine,
            $storage
        );
        $subscriber->beforeActionConfiguration($event);
    }

    public function testBeforeConfigurationWithoutFields()
    {
        $applicationConfiguration = $this->getMockWithoutConstructor(ApplicationConfiguration::class);
        $applicationConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['translation', [
                    'pattern' => '{admin}.{key}'
                ]],
            ])
        ;
        
        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->willReturnMap([
                ['actions', []],
                ['entity', TestSimpleEntity::class],
            ])
        ;
        $metadata = $this->getMockWithoutConstructor(ClassMetadata::class);
        $metadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->willReturn([
                'id' => [],
                'name' => [],
            ])
        ;
        $metadataFactory = $this->getMockWithoutConstructor(ClassMetadataFactory::class);
        $metadataFactory
            ->expects($this->once())
            ->method('getMetadataFor')
            ->willReturn($metadata)
        ;
        $entityManager = $this->getMockWithoutConstructor(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory)
        ;
        $doctrine = $this->getMockWithoutConstructor(Registry::class);
        $doctrine
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($entityManager)
        ;
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $event = $this->getMockWithoutConstructor(BeforeConfigurationEvent::class);
        $event
            ->expects($this->once())
            ->method('getActionConfiguration')
            ->willReturn([])
        ;
        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $storage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
        $storage
            ->expects($this->once())
            ->method('getApplicationConfiguration')
            ->willReturn($applicationConfiguration)
        ;

        $subscriber = new ExtraConfigurationSubscriber(
            true,
            $doctrine,
            $storage
        );

        $subscriber->beforeActionConfiguration($event);
    }
}
