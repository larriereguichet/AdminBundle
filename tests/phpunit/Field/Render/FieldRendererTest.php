<?php

namespace LAG\AdminBundle\Tests\Field\Render;

use Exception;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Exception\View\FieldRenderingException;
use LAG\AdminBundle\Field\Render\FieldRenderer;
use LAG\AdminBundle\Field\View\TextView;
use LAG\AdminBundle\Field\View\View;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Twig\Environment;
use function Symfony\Component\String\u;

class FieldRendererTest extends TestCase
{
    private FieldRenderer $renderer;
    private MockObject $environment;
    private ApplicationConfiguration $applicationConfiguration;

    public function testService()
    {
        $this->assertServiceExists(FieldRenderer::class);
    }

    public function testRender(): void
    {
        $field = $this->createMock(View::class);
        $field
            ->expects($this->exactly(3))
            ->method('getOption')
            ->willReturnMap([
                ['property_path', 'my_property'],
                ['mapped', true],
            ])
        ;
        $field
            ->expects($this->once())
            ->method('getDataTransformer')
            ->willReturn(function ($value) {
                return u($value)->title()->toString();
            })
        ;
        $field
            ->expects($this->atLeastOnce())
            ->method('getTemplate')
            ->willReturn('@LAGAdmin/fields/string.html.twig')
        ;
        $field
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('my_field')
        ;

        $data = new stdClass();
        $data->title = true;
        $data->my_property = 'my_value';

        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($template, $context) use($data) {
                $this->assertEquals('@LAGAdmin/fields/string.html.twig', $template);
                $this->assertEquals([
                    'data' => 'My_value',
                    'name' => 'my_field',
                    'object' => $data,
                    'options' => [],
                ], $context);

                return ' <p>Content</p>';
            })
        ;

        $content = $this->renderer->render($field, $data);
        $this->assertEquals('<p>Content</p>', $content);
    }

    public function testRenderTextView(): void
    {
        $field = $this->createMock(TextView::class);
        $field
            ->expects($this->exactly(3))
            ->method('getOption')
            ->willReturnMap([
                ['property_path', 'my_property'],
                ['mapped', true],
            ])
        ;
        $field
            ->expects($this->once())
            ->method('getDataTransformer')
            ->willReturn(function ($value) {
                return u($value)->title()->toString();
            })
        ;
        $data = new stdClass();
        $data->my_property = 'my_value';

        $this->renderer->render($field, $data);
    }

    public function testRenderWithException(): void
    {
        $field = $this->createMock(View::class);
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
        $this->renderer->render($field, $data);
    }

    /**
     * @dataProvider renderHeaderDataProvider
     */
    public function testRenderHeader($label, string $name, string $expectedData): void
    {
        $field = $this->createMock(View::class);
        $field
            ->expects($this->once())
            ->method('getOption')
            ->with('label')
            ->willReturn($label)
        ;
        $field
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($name)
        ;
        $field
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn([
                'my_option' => true,
            ])
        ;
        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->with('@LAGAdmin/fields/header.html.twig', [
                'data' => $expectedData,
                'name' => $name,
                'options' => [
                    'my_option' => true,
                ],
            ])
            ->willReturn('Some content')
        ;

        $render = $this->renderer->renderHeader($field);
        $this->assertEquals('Some content', $render);
    }

    public function testRenderHeaderWithException(): void
    {
        $field = $this->createMock(View::class);
        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->willThrowException(new \LAG\AdminBundle\Exception\Exception())
        ;

        $this->expectException(FieldRenderingException::class);
        $this->renderer->renderHeader($field);
    }

    public function renderHeaderDataProvider(): array
    {
        return [
            ['My Label', 'title', 'My Label'],
            [null, 'title', 'title'],
            ['My Label', '_title', ''],
            [false, 'title', ''],
            [false, 'id', '#'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->environment = $this->createMock(Environment::class);
        $this->applicationConfiguration = new ApplicationConfiguration(['resources_path' => 'test']);
        $this->renderer = new FieldRenderer($this->environment, $this->applicationConfiguration);
    }
}
