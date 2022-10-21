<?php

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Extension\AdminExtension;
use LAG\AdminBundle\Twig\Render\ActionRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;

class AdminExtensionTest extends TestCase
{
    private AdminExtension $adminExtension;
    private MockObject $configuration;
    private MockObject $security;
    private MockObject $actionRenderer;
    private MockObject $urlGenerator;

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

    public function testGetParameter(): void
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

    public function testIsAdminActionAllowed(): void
    {
        $this
            ->security
            ->expects($this->once())
            ->method('isOperationAllowed')
            ->with('my_admin', 'my_action')
        ;
        $this->adminExtension->isOperationAllowed('my_admin', 'my_action');
    }

    public function testRenderAction(): void
    {
        $action = new Action();
        $data = new \stdClass();
        $options = ['an_option' => 'a_value'];

        $this
            ->actionRenderer
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
        $operation = (new Index())
            ->withName('my_operation')
            ->withResourceName('my_resource')
        ;
        $data = new \stdClass();

        $this
            ->urlGenerator
            ->expects($this->once())
            ->method('generateFromOperationName')
            ->with('my_resource', 'my_operation', $data)
            ->willReturn('/url')
        ;

        $url = $this->adminExtension->getOperationUrl($operation, $data);

        $this->assertEquals('/url', $url);
    }

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(ApplicationConfiguration::class);
        $this->security = $this->createMock(SecurityHelper::class);
        $this->actionRenderer = $this->createMock(ActionRendererInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->adminExtension = new AdminExtension(
            $this->configuration,
            $this->security,
            $this->actionRenderer,
            $this->urlGenerator,
        );
    }
}
