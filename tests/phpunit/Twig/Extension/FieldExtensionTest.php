<?php

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Field\Render\FieldRendererInterface;
use LAG\AdminBundle\Field\View\FieldView;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Extension\FieldExtension;
use LAG\AdminBundle\View\AdminView;
use PHPUnit\Framework\MockObject\MockObject;

class FieldExtensionTest extends TestCase
{
    private MockObject $renderer;
    private FieldExtension $extension;

    public function testServiceExists(): void
    {
        $this->assertServiceExists(FieldExtension::class);
    }

    public function testRender(): void
    {
        $field = $this->createMock(FieldView::class);
        $entity = new FakeEntity();

        $this
            ->renderer
            ->expects($this->once())
            ->method('render')
            ->with($field, $entity)
            ->willReturn('<p>A beautiful bamboo</p>')
        ;

        $this->assertEquals('<p>A beautiful bamboo</p>', $this->extension->renderField($field, $entity));
    }

    public function testRenderHeader(): void
    {
        $field = $this->createMock(FieldView::class);
        $admin = $this->createMock(AdminView::class);

        $this
            ->renderer
            ->expects($this->once())
            ->method('renderHeader')
            ->with($admin, $field)
            ->willReturn('<p>A beautiful bamboo</p>')
        ;

        $this->assertEquals('<p>A beautiful bamboo</p>', $this->extension->renderFieldHeader($admin, $field));
    }

    public function testGetFunctions()
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertEquals('admin_field', $functions[0]->getName());
        $this->assertEquals('admin_field_header', $functions[1]->getName());
    }

    protected function setUp(): void
    {
        $this->renderer = $this->createMock(FieldRendererInterface::class);
        $this->extension = new FieldExtension($this->renderer);
    }
}
