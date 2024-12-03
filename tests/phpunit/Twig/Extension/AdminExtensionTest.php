<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Grid\Render\LinkRendererInterface;
use LAG\AdminBundle\Resource\Metadata\Link;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Extension\RenderExtension;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\SecurityBundle\Security;

final class AdminExtensionTest extends TestCase
{
    private RenderExtension $adminExtension;
    private MockObject $security;
    private MockObject $linkRenderer;
    private MockObject $urlGenerator;
    private MockObject $registry;

    public function testIsOperationAllowed(): void
    {
        $operation = new Show(name: 'my_operation');
        $resource = new Resource(name: 'my_resource', operations: [$operation]);

        $this
            ->registry
            ->expects(self::once())
            ->method('get')
            ->with('my_resource')
            ->willReturn($resource)
        ;

        $this
            ->security
            ->expects(self::once())
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
            ->expects(self::once())
            ->method('render')
            ->with($action)
            ->willReturn('<p>content</p>')
        ;

        $content = $this->adminExtension->renderLink($action, $data, $options);

        $this->assertEquals('<p>content</p>', $content);
    }

    public function testGetOperationUrl(): void
    {
        $data = new \stdClass();

        $this
            ->urlGenerator
            ->expects(self::once())
            ->method('generateFromOperationName')
            ->with('my_resource', 'my_operation', $data)
            ->willReturn('/url')
        ;

        $url = $this->adminExtension->generateUrl('my_resource', 'my_operation', $data);

        $this->assertEquals('/url', $url);
    }

    protected function setUp(): void
    {
        $this->security = self::createMock(Security::class);
        $this->linkRenderer = self::createMock(LinkRendererInterface::class);
        $this->urlGenerator = self::createMock(UrlGeneratorInterface::class);
        $this->registry = self::createMock(ResourceRegistryInterface::class);
        $this->adminExtension = new RenderExtension(
            $this->security,
            $this->linkRenderer,
            $this->urlGenerator,
            $this->registry,
        );
    }
}
