<?php

namespace LAG\AdminBundle\Tests\Bridge\Twig\Extension;

use LAG\AdminBundle\Bridge\Twig\Extension\FieldExtension;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\Render\FieldRendererInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use LAG\AdminBundle\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Twig_Function;

class FieldExtensionTest extends AdminTestBase
{
    public function testServiceExists()
    {
        $this->assertServiceExists(FieldExtension::class);
    }

    public function testRender()
    {
        list($extension, $renderer) = $this->createExtension();

        $field = $this->createMock(FieldInterface::class);
        $entity = new FakeEntity();

        $renderer
            ->expects($this->once())
            ->method('render')
            ->with($field, $entity)
            ->willReturn('<p>A beautiful bamboo</p>')
        ;

        $this->assertEquals('<p>A beautiful bamboo</p>', $extension->renderField($field, $entity));
    }

    public function testRenderHeader()
    {
        list($extension, $renderer) = $this->createExtension();

        $field = $this->createMock(FieldInterface::class);
        $admin = $this->createMock(ViewInterface::class);

        $renderer
            ->expects($this->once())
            ->method('renderHeader')
            ->with($admin, $field)
            ->willReturn('<p>A beautiful bamboo</p>')
        ;

        $this->assertEquals('<p>A beautiful bamboo</p>', $extension->renderFieldHeader($admin, $field));
    }

    public function testGetFunctions()
    {
        list($extension) = $this->createExtension();

        /** @var Twig_Function[] $functions */
        $functions = $extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertEquals('admin_field', $functions[0]->getName());
        $this->assertEquals('admin_field_header', $functions[1]->getName());
    }

    /**
     * @return FieldExtension[]|MockObject[]
     */
    private function createExtension(): array
    {
        $renderer = $this->createMock(FieldRendererInterface::class);
        $extension = new FieldExtension($renderer);

        return [
            $extension,
            $renderer,
        ];
    }
}
