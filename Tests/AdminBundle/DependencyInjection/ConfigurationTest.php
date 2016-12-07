<?php

namespace LAG\AdminBundle\Tests\AdminBundle\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\Configuration;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Config\Definition\ArrayNode;

class ConfigurationTest extends AdminTestBase
{
    /**
     * GetConfigTreeBuilder method should a return valid array nodes. The configuration is more tested in
     * LagAdminExtensionTest.
     */
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $tree = $configuration->getConfigTreeBuilder();
        /** @var ArrayNode $arrayNode */
        $arrayNode = $tree->buildTree();
        $this->assertInstanceOf(ArrayNode::class, $arrayNode);

        $arrayConfiguration = $arrayNode->getChildren();

        // test application configuration
        $this->assertArrayHasKey('application', $arrayConfiguration);
        $this->assertInstanceOf(ArrayNode::class, $arrayNode->getChildren()['application']);
        $this->assertArrayHasKey('admins', $arrayConfiguration);
        $this->assertInstanceOf(ArrayNode::class, $arrayNode->getChildren()['admins']);
        $this->assertArrayHasKey('menus', $arrayConfiguration);
        $this->assertInstanceOf(ArrayNode::class, $arrayNode->getChildren()['menus']);
    }
}
