<?php

namespace LAG\AdminBundle\Tests\Field\Render;

use Exception;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Exception\View\FieldRenderingException;
use LAG\AdminBundle\Field\Render\FieldRenderer;
use LAG\AdminBundle\Field\Render\FieldRendererInterface;
use LAG\AdminBundle\Tests\Field\FieldTestCase;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use LAG\AdminBundle\View\AdminView;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Twig\Environment;

class FieldRendererTest extends FieldTestCase
{
    private FieldRendererInterface $renderer;
    private MockObject $environment;
    private MockObject $translator;

    public function testRender(): void
    {
        $field = $this->factory->create('title', [
            'type' => 'string',
            'options' => ['mapped' => true],
        ]);
        $data = new stdClass();
        $data->title = true;

        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->with('@LAGAdmin/fields/string.html.twig', [
                'data' => true,
                'name' => 'title',
                'object' => $data,
                'options' => [
                    'length' => 200,
                    'replace' => '...',
                    'translate_title' => true,
                    'attr' => [
                        'class' => 'admin-field admin-field-string',
                    ],
                    'header_attr' => [
                        'class' => 'admin-header admin-header-string',
                    ],
                    'label' => null,
                    'mapped' => true,
                    'property_path' => 'title',
                    'template' => '@LAGAdmin/fields/string.html.twig',
                    'translation' => false,
                    'translation_domain' => null,
                    'sortable' => true,
                ],
            ])
            ->willReturn(' <p>Content</p>')
        ;
        $content = $this->renderer->render($field->createView(), $data);

        $this->assertEquals('<p>Content</p>', $content);
    }

    public function testRenderWithDataTransformer(): void
    {
        $field = $this->factory->create('title', ['type' => 'array']);

        $this
            ->environment
            ->expects($this->never())
            ->method('render')
        ;

        $data = new stdClass();
        $data->title = ['my', 'little', 'string'];

        $content = $this->renderer->render($field->createView(), $data);

        $this->assertEquals('my, little, string', $content);
    }

    public function testRenderWithException(): void
    {
        $field = $this->factory->create('title', [
            'type' => 'string',
            'options' => ['mapped' => true],
        ]);
        $data = new stdClass();
        $data->title = true;

        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function () {
                throw new Exception();
            })
        ;

        $this->expectException(FieldRenderingException::class);
        $this->renderer->render($field->createView(), $data);
    }

    /**
     * @dataProvider renderHeaderDataProvider
     */
    public function testRenderHeader(
        string $name,
        string $type,
        array $options,
        bool $isTranslationEnabled,
        string $expectedLabel
    ): void {
        $field = $this->factory->create($name, [
            'type' => $type,
            'options' => $options,
        ]);
        $admin = $this->createMock(AdminView::class);

        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function (string $template, array $options) use ($name, $expectedLabel) {
                $this->assertEquals('@LAGAdmin/fields/header.html.twig', $template);

                $this->assertArrayHasKey('data', $options);
                $this->assertEquals($expectedLabel, $options['data']);

                $this->assertArrayHasKey('name', $options);
                $this->assertEquals($name, $options['name']);

                return 'my_content';
            })
        ;

        if ($isTranslationEnabled) {
            $configuration = $this->createMock(AdminConfiguration::class);
            $configuration
                ->expects($this->once())
                ->method('isTranslationEnabled')
                ->willReturn(true)
            ;
            $admin
                ->expects($this->once())
                ->method('getAdminConfiguration')
                ->willReturn($configuration)
            ;
            $this
                ->translator
                ->expects($this->once())
                ->method('transWithPattern')
                ->willReturn($expectedLabel)
            ;
        }
        $render = $this->renderer->renderHeader($admin, $field->createView());

        $this->assertEquals('my_content', $render);
    }

    public function renderHeaderDataProvider(): array
    {
        return [
            ['title', 'string', [], false, 'Title'],
            ['name', 'string', ['label' => false], false, ''],
            ['id', 'string', ['label' => false], false, '#'],
            ['id', 'string', ['label' => 'MyLabel'], false, 'MyLabel'],
            ['id', 'string', ['label' => null], true, 'MyLabel'],
        ];
    }

    public function testRenderHeaderWithException(): void
    {
        $field = $this->factory->create('title', [
            'type' => 'string',
        ]);
        $admin = $this->createMock(AdminView::class);

        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function (string $template, array $options) {
                throw new Exception();
            })
        ;
        $this->expectException(FieldRenderingException::class);
        $this->renderer->renderHeader($admin, $field->createView());
    }

//    public function testRenderWithRendererAware()
//    {
//        [$renderer] = $this->createRender();
//
//        $entity = new FakeEntity(666);
//        $field = $this->createMock(RendererAwareFieldInterface::class);
//        $field
//            ->expects($this->exactly(2))
//            ->method('getName')
//            ->willReturn('id')
//        ;
//        $field
//            ->expects($this->once())
//            ->method('setRenderer')
//            ->willReturn($entity)
//        ;
//        $field
//            ->expects($this->once())
//            ->method('render')
//            ->willReturn('<p>My Little Content</p>')
//        ;
//        $render = $renderer->render($field, $entity);
//
//        $this->assertEquals('<p>My Little Content</p>', $render);
//    }
//
//    public function testRenderHeader()
//    {
//        [$renderer, $translator] = $this->createRender();
//
//        $adminConfiguration = $this->createMock(AdminConfiguration::class);
//
//        $admin = $this->createMock(ViewInterface::class);
//        $admin
//            ->expects($this->exactly(1))
//            ->method('getName')
//            ->willReturn('Admin')
//        ;
//        $admin
//            ->expects($this->exactly(1))
//            ->method('getAdminConfiguration')
//            ->willReturn($adminConfiguration)
//        ;
//        $adminConfiguration
//            ->expects($this->atLeastOnce())
//            ->method('isTranslationEnabled')
//            ->willReturn(true)
//        ;
//        $adminConfiguration
//            ->expects($this->atLeastOnce())
//            ->method('getTranslationPattern')
//            ->willReturn('{admin}.{key}')
//        ;
//
//        $field = $this->createMock(FieldInterface::class);
//        $field
//            ->expects($this->exactly(2))
//            ->method('getName')
//            ->willReturn('name')
//        ;
//        $translator
//            ->expects($this->exactly(1))
//            ->method('trans')
//            ->with('Admin.name')
//            ->willReturn('My Translated String')
//        ;
//
//        $render = $renderer->renderHeader($admin, $field);
//
//        $this->assertEquals('My Translated String', $render);
//    }
//
//    public function testRenderHeaderWithNoMappedField()
//    {
//        [$renderer] = $this->createRender();
//
//        $admin = $this->createMock(ViewInterface::class);
//        $field = $this->createMock(FieldInterface::class);
//        $field
//            ->expects($this->exactly(1))
//            ->method('getName')
//            ->willReturn('_actions')
//        ;
//
//        $render = $renderer->renderHeader($admin, $field);
//
//        $this->assertEquals('', $render);
//    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->environment = $this->createMock(Environment::class);
        $this->translator = $this->createMock(TranslationHelperInterface::class);
        $this->renderer = new FieldRenderer($this->environment, $this->translator);
    }
}
