<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Configuration;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Configuration\ConfigurationException;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionConfigurationTest extends AdminTestBase
{
    public function testActionConfiguration()
    {
//        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
//        $adminConfiguration
//            ->expects($this->atLeastOnce())
//            ->method('getParameter')
//            ->with('translation_pattern')
//            ->willReturn('{key}')
//        ;
//
//        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
//        $admin
//            ->expects($this->once())
//            ->method('getConfiguration')
//            ->willReturn($adminConfiguration)
//        ;
//
//        $resolver = new OptionsResolver();
//
//        $configuration = new ActionConfiguration('an_action', $admin);
//        $configuration->configureOptions($resolver);
//
//        $resolver->resolve([]);
    }
    
//    public function testFieldNormalizer()
//    {
//        $admin = $this->createAdmin('an_admin', [
//            'entity' => 'WhatEver',
//            'form' => 'AForm',
//            'actions' => [
//                'an_action' => [],
//            ],
//        ]);
//        $resolver = new OptionsResolver();
//
//        $configuration = new ActionConfiguration('an_action', $admin);
//        $configuration->configureOptions($resolver);
//
//        $options = $resolver->resolve([
//            'fields' => [
//                // test default field mapping
//                'aField' => null,
//            ],
//        ]);
//
//        $this->assertEquals([], $options['fields']['aField']);
//    }
//
//    public function testRouteNormalizer()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//        $configuration = new ActionConfiguration('an_action', $admin);
//        $configuration->configureOptions($resolver);
//
//        $resolver->resolve();
//    }
//
//    public function testRouteNormalizerWithValue()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//        $configuration = new ActionConfiguration('an_action', $admin);
//        $configuration->configureOptions($resolver);
//
//        $options = $resolver->resolve([
//            'route' => 'my_custom.route',
//        ]);
//
//        // th value should be unchanged
//        $this->assertEquals($options['route'], 'my_custom.route');
//    }
//
//    public function testOrderNormalizer()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//        $configuration = new ActionConfiguration('an_action', $admin);
//        $configuration->configureOptions($resolver);
//
//        // an exception should be thrown if an invalid order configuration is passed
//        $this->assertExceptionRaised(ConfigurationException::class, function() use ($resolver) {
//            $resolver->resolve([
//                'order' => [
//                    'id' => 'ASK',
//                ],
//            ]);
//        });
//    }
//
//    public function testMenuNormalizer()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//        $configuration = new ActionConfiguration('an_action', $admin);
//        $configuration->configureOptions($resolver);
//
//        $options = $resolver->resolve([
//            'menus' => false,
//        ]);
//
//        $this->assertEquals([], $options['menus']);
//    }
//
//    public function testBatchNormalizer()
//    {
//        $admin = $this->getAdminMockForActionConfiguration([
//            ['actions', [
//                'batch' => [],
//                'list' => [],
//                'delete' => [],
//            ]],
//        ]);
//        $resolver = new OptionsResolver();
//        $configuration = new ActionConfiguration('list', $admin);
//        $configuration->configureOptions($resolver);
//
//        $options = $resolver->resolve([
//            'batch' => null,
//        ]);
//        $this->assertArrayHasKey('attr', $options['batch']);
//        $this->assertArrayHasKey('items', $options['batch']);
//    }
//
//    public function testBatchNormalizerWithoutDelete()
//    {
//        $admin = $this->getAdminMockForActionConfiguration([
//            ['actions', [
//                'batch' => [],
//                'list' => [],
//            ]],
//        ]);
//        $resolver = new OptionsResolver();
//        $configuration = new ActionConfiguration('list', $admin);
//        $configuration->configureOptions($resolver);
//
//        $options = $resolver->resolve([
//            'batch' => null,
//        ]);
//        $this->assertNull($options['batch']);
//    }
//
//    public function testTitle()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//
//        $configuration = new ActionConfiguration('edit', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals('Edit', $options['title']);
//    }
//
//    public function testTitleWithTranslationPattern()
//    {
//        $admin = $this->getAdminMockForActionConfiguration([
//            ['translation_pattern', 'pattern.test.{key}'],
//        ]);
//        $resolver = new OptionsResolver();
//
//        $configuration = new ActionConfiguration('edit', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals('pattern.test.edit', $options['title']);
//    }
//
//    public function testRouteDefaultPath()
//    {
//        $admin = $this->getAdminMockForActionConfiguration([
//            ['routing_url_pattern', '{admin}/custom/{action}'],
//        ]);
//        $resolver = new OptionsResolver();
//
//        // test "normal" actions
//        $configuration = new ActionConfiguration('list', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals('tauntaun/custom/list', $options['route_path']);
//
//        // test unchanged if provided
//        $configuration = new ActionConfiguration('delete', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([
//            'route_path' => 'custom/route',
//        ]);
//
//        $this->assertEquals('custom/route', $options['route_path']);
//    }
//
//    public function testRouteDefaultPathForEdit()
//    {
//        $admin = $this->getAdminMockForActionConfiguration([
//            ['routing_url_pattern', '{admin}/custom/{action}'],
//        ]);
//        $resolver = new OptionsResolver();
//
//        // test edit action
//        $configuration = new ActionConfiguration('edit', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals('tauntaun/custom/edit/{id}', $options['route_path']);
//
//    }
//
//    public function testRouteDefaultPathForDelete()
//    {
//        $admin = $this->getAdminMockForActionConfiguration([
//            ['routing_url_pattern', '{admin}/custom/{action}'],
//        ]);
//        $resolver = new OptionsResolver();
//
//        // test delete action
//        $configuration = new ActionConfiguration('delete', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals('tauntaun/custom/delete/{id}', $options['route_path']);
//    }
//
//    public function testRouteDefaultDefaults()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//
//        // test "normal" actions
//        $configuration = new ActionConfiguration('custom', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals([], $options['route_defaults']);
//    }
//
//    public function testRouteDefaultDefaultsForList()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//
//        // test list actions
//        $configuration = new ActionConfiguration('list', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals([
//            '_controller' => LAGAdminBundle::SERVICE_ID_LIST_ACTION,
//            '_admin' => 'tauntaun',
//            '_action' => 'list',
//        ], $options['route_defaults']);
//    }
//
//    public function testRouteDefaultDefaultsForCreate()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//
//        // test create actions
//        $configuration = new ActionConfiguration('create', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals([
//            '_controller' => LAGAdminBundle::SERVICE_ID_CREATE_ACTION,
//            '_admin' => 'tauntaun',
//            '_action' => 'create',
//        ], $options['route_defaults']);
//    }
//
//    public function testRouteDefaultDefaultsForEdit()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//
//        // test edit actions
//        $configuration = new ActionConfiguration('edit', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals([
//            '_controller' => LAGAdminBundle::SERVICE_ID_EDIT_ACTION,
//            '_admin' => 'tauntaun',
//            '_action' => 'edit',
//        ], $options['route_defaults']);
//    }
//
//    public function testRouteDefaultDefaultsForDelete()
//    {
//        $admin = $this->getAdminMockForActionConfiguration();
//        $resolver = new OptionsResolver();
//
//        // test delete actions
//        $configuration = new ActionConfiguration('delete', $admin);
//        $configuration->configureOptions($resolver);
//        $options = $resolver->resolve([]);
//
//        $this->assertEquals([
//            '_controller' => LAGAdminBundle::SERVICE_ID_DELETE_ACTION,
//            '_admin' => 'tauntaun',
//            '_action' => 'delete',
//        ], $options['route_defaults']);
//    }
//
//    protected function getAdminMockForActionConfiguration($adminConfigurationReturnMap = [])
//    {
//        $adminConfiguration = $this
//            ->getMockBuilder(AdminConfiguration::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//        $adminConfiguration
//            ->method('getParameter')
//            ->willReturnMap($adminConfigurationReturnMap);
//        $admin = $this
//            ->getMockBuilder(Admin::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//        $admin
//            ->method('getName')
//            ->willReturn('tauntaun');
//        $admin
//            ->method('getConfiguration')
//            ->willReturn($adminConfiguration);
//
//        return $admin;
//    }
}
