<?php

namespace LAG\AdminBundle\Tests\Field\Render;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Field\EntityAwareFieldInterface;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\Render\FieldRenderer;
use LAG\AdminBundle\Field\RendererAwareFieldInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use LAG\AdminBundle\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\Translation\TranslatorInterface;

class FieldRendererTest extends AdminTestBase
{
    public function testRender()
    {
        list($renderer) = $this->createRender();

        $entity = new FakeEntity(666);
        $field = $this->createMock(EntityAwareFieldInterface::class);
        $field
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('id')
        ;
        $field
            ->expects($this->once())
            ->method('setEntity')
            ->willReturn($entity)
        ;
        $field
            ->expects($this->once())
            ->method('render')
            ->willReturn('<p>My Little Content</p>')
        ;
        $render = $renderer->render($field, $entity);

        $this->assertEquals('<p>My Little Content</p>', $render);
    }

    public function testRenderWithRendererAware()
    {
        list($renderer) = $this->createRender();

        $entity = new FakeEntity(666);
        $field = $this->createMock(RendererAwareFieldInterface::class);
        $field
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('id')
        ;
        $field
            ->expects($this->once())
            ->method('setRenderer')
            ->willReturn($entity)
        ;
        $field
            ->expects($this->once())
            ->method('render')
            ->willReturn('<p>My Little Content</p>')
        ;
        $render = $renderer->render($field, $entity);

        $this->assertEquals('<p>My Little Content</p>', $render);
    }

    public function testRenderHeader()
    {
        list($renderer, $storage, $translator) = $this->createRender();

        $admin = $this->createMock(ViewInterface::class);
        $admin
            ->expects($this->exactly(1))
            ->method('getName')
            ->willReturn('Admin')
        ;
        $field = $this->createMock(FieldInterface::class);
        $field
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('name')
        ;
        $configuration = $this->createMock(ApplicationConfiguration::class);
        $configuration
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['translation', true],
                ['translation_pattern', '{admin}.{key}'],
            ])
        ;
        $translator
            ->expects($this->exactly(1))
            ->method('trans')
            ->with('Admin.name')
            ->willReturn('My Translated String')
        ;
        $storage
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        $render = $renderer->renderHeader($admin, $field);

        $this->assertEquals('My Translated String', $render);
    }

    public function testRenderHeaderWithNoMappedField()
    {
        list($renderer,,) = $this->createRender();

        $admin = $this->createMock(ViewInterface::class);
        $field = $this->createMock(FieldInterface::class);
        $field
            ->expects($this->exactly(1))
            ->method('getName')
            ->willReturn('_actions')
        ;

        $render = $renderer->renderHeader($admin, $field);

        $this->assertEquals('', $render);
    }

    /**
     * @return FieldRenderer[]|MockObject[]
     */
    private function createRender(): array
    {
        $storage = $this->createMock(ApplicationConfigurationStorage::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $render = new FieldRenderer($storage, $translator);

        return [
            $render,
            $storage,
            $translator
        ];
    }
}
