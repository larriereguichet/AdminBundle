<?php

namespace LAG\AdminBundle\Tests\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\Configuration;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\Config\Definition\ArrayNode;

class ConfigurationTest extends TestCase
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

        $this->assertArrayHasKey('title', $arrayNode->getChildren());
        $this->assertArrayHasKey('description', $arrayNode->getChildren());
    }
}
