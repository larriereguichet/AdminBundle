<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Grid\View\LinkRendererInterface;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Get;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Extension\AdminExtension;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\SecurityBundle\Security;

class AdminExtensionTest extends TestCase
{
    private AdminExtension $adminExtension;
    private MockObject $configuration;
    private MockObject $security;
    private MockObject $linkRenderer;
    private MockObject $urlGenerator;
    private MockObject $registry;

    public function testServiceExists(): void
    {
        $this->assertServiceExists(AdminExtension::class);
    }

    public function testGetFunctions(): void
    {
        foreach ($this->adminExtension->getFunctions() as $function) {
            $this->assertContains($function->getName(), [
                'lag_admin_config',
                'lag_admin_operation_allowed',
                'lag_admin_action',
                'lag_admin_operation_url',
            ]);
            $this->assertTrue(method_exists($this->adminExtension, $function->getCallable()[1]));
        }
    }

    public function testGetConfigurationValue(): void
    {
        $this
            ->configuration
            ->expects($this->once())
            ->method('get')
            ->with('my_parameter')
            ->willReturn('my_value')
        ;

        $this->assertEquals('my_value', $this->adminExtension->getConfigurationValue('my_parameter'));
    }

    public function testIsOperationAllowed(): void
    {
        $operation = new Get(name: 'my_operation');
        $resource = new AdminResource(name: 'my_resource', operations: [$operation]);

        $this
            ->registry
            ->expects($this->once())
            ->method('get')
            ->with('my_resource')
            ->willReturn($resource)
        ;

        $this
            ->security
            ->expects($this->once())
            ->method('isGranted')
            ->with(OperationPermissionVoter::RESOURCE_ACCESS, $operation)
        ;

        $this->adminExtension->isOperationAllowed('my_resource', 'my_operation');
    }

    public function testRenderAction(): void
    {
        $action = new Link();
        $data = new \stdClass();
        $options = ['an_option' => 'a_value'];

        $this
            ->linkRenderer
            ->expects($this->once())
            ->method('render')
            ->with($action)
            ->willReturn('<p>content</p>')
        ;

        $content = $this->adminExtension->renderAction($action, $data, $options);

        $this->assertEquals('<p>content</p>', $content);
    }

    public function testGetOperationUrl(): void
    {
        $data = new \stdClass();

        $this
            ->urlGenerator
            ->expects($this->once())
            ->method('generateFromOperationName')
            ->with('my_resource', 'my_operation', $data)
            ->willReturn('/url')
        ;

        $url = $this->adminExtension->getOperationUrl('my_resource', 'my_operation', $data);

        $this->assertEquals('/url', $url);
    }

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(ApplicationConfiguration::class);
        $this->security = $this->createMock(Security::class);
        $this->linkRenderer = $this->createMock(LinkRendererInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->registry = $this->createMock(ResourceRegistryInterface::class);
        $this->adminExtension = new AdminExtension(
            $this->configuration,
            $this->security,
            $this->linkRenderer,
            $this->urlGenerator,
            $this->registry,
        );
    }
}
